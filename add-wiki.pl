#!/usr/bin/perl
use strict;
use warnings;
use LWP::Debug qw(+);
# A simple example on how to migrate a TWiki to Google Sites automatically.
# Copyright (C) 2010 Ivan Zahariev (famzah)
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.

use LWP; # http://www.perl.com/pub/a/2002/08/20/perlandlwp.html
use HTML::Entities;
use URI::Escape;
use Data::Dumper;
use XML::Simple;

use HTTP::Request::Common qw(POST);
use LWP::UserAgent;
use Data::Dumper qw(Dumper);
use Time::Local;
use DBI;

my $ini="/home/sites/www.blueprinthaus.org/account/settings.ini";
my %hash;
my $section;
open (INI, "$ini") || die "Can't open $ini: $!\n";
while (<INI>) {
     chomp;
     if (/^\s*\[(\w+)\].*/) {
                $section = $1;
            }
            if (/^(\w+)\s*=\s*'(\w+)'$/) {
                my $keyword = $1;
                my $value = $2;
                # put them into hash
				$hash{$section}{$keyword}= $value;
            }
        }
close (INI);

my $GoogleEmail = $hash{'google'}{'user'};
my $GooglePasswd = $hash{'google'}{'password'};
my $GoogleDomainName = 'site';
my $GoogleSiteName = '30nover';
my @webs_to_import = qw/ CppMysql Development Electronics Linux RealLife /;
my @ignore_pages = qw/ WebAtom WebChanges WebHome WebIndex WebLeftBar WebNotify WebPreferences /;
my $ua;
my $atoken;

#my $filename1 = "members.log";
#open(LOG, '>>', $filename1);

sub http_req($$$;$$$) {
    my ($type, $url, $form_values, $headers, $content, $possible_error_codes) = @_;
    my $response;
    my @lines;
    my $request;
    my $hdrname;
    my $get_s;
    my ($k, $v);

    if (!defined($headers)) {
        $headers = [];
    }
    if (scalar @{$headers} % 2 != 0) {
        die("Headers not even");
    }

    if ($type eq 'POST') { # no support for custom headers
        $response = $ua->post($url, $form_values);
    } elsif ($type eq 'GET') {
        $get_s = '';
        while (($k, $v) = each %{$form_values}) {
            $get_s = $get_s.sprintf('%s=%s&', uri_escape($k), uri_escape($v));
        }
        if (length($get_s)) {
            $get_s = "?$get_s";
        }
        $response = $ua->get("$url$get_s", @{$headers});
    } elsif ($type eq 'XML') {
        $request = HTTP::Request->new(POST => $url);
        $request->content_type('application/atom+xml');
        while (scalar @{$headers}) {
            $hdrname = shift @{$headers};
            $request->header($hdrname, shift @{$headers});
        }
        $request->content($content);
        $response = $ua->request($request);
    } elsif ($type eq 'DELETE') {
	$request = HTTP::Request->new(DELETE => $url);
	while (scalar @{$headers}) {
            $hdrname = shift @{$headers};
            $request->header($hdrname, shift @{$headers});
        }
	$response = $ua->request($request);
    } else {
        die("Bad request type: $type");
    }

    if (!defined($possible_error_codes)) {
        $possible_error_codes = [];
    }

    if (!$response->is_success) {
        if (!grep({$response->code == $_} @{$possible_error_codes})) {
            print Dumper($content)."\n";
            die("$type request to '$url' failed: ".$response->status_line.": ".$response->content);
        }
    }

    @lines = split(/\n/, $response->content);
    return ($response->code, \@lines, $response);
}

sub auth() {
    my ($r_code, $r_lines);

    ($r_code, $r_lines) = http_req('POST',
            'https://www.google.com/accounts/ClientLogin',
            {
            'accountType' => 'HOSTED_OR_GOOGLE',
            'Email' => $GoogleEmail,
            'Passwd' => $GooglePasswd,
            'service' => 'jotspot', # http://code.google.com/intl/bg/apis/gdata/faq.html#clientlogin
            'source' => 'Testing',
            },
            [], # headers
            [], # content
            [403],
            );

    if ($r_code == 403) {
        die(
                "Authentication failed, see http://code.google.com/intl/bg/apis/accounts/docs/AuthForInstalledApps.html ".
                "for more info on how to react:\n".Dumper($r_lines)
           );
    }

    foreach (@{$r_lines}) {
        if ($_ =~ /^Auth=(\S+)$/) {
            return $1;
        }
    }

    die("Authentication failed. HTTP code was successful but the body contains no 'Auth':\n".Dumper($r_lines));
}

sub add_acls {
	my $new_email=shift;
	chomp($new_email);
    my ($r_code, $r_lines);
    my $req_url;

    $req_url = "https://sites.google.com/feeds/acl/site/site/30nover";
    my $req_content = <<EOF;
    <entry xmlns="http://www.w3.org/2005/Atom" xmlns:gAcl='http://schemas.google.com/acl/2007'>
      <category scheme='http://schemas.google.com/g/2005#kind'
        term='http://schemas.google.com/acl/2007#accessRule'/>
      <gAcl:role value='writer'/>
      <gAcl:scope type='user' value='$new_email'/>
    </entry>
EOF
    do {
        #print "REQUEST: $req_url\n";
        #print "CONTENT: $req_content\n";
        ($r_code, $r_lines) = http_req('XML',
                $req_url,
                {},
                [
                'GData-Version' => '1.2',
                'Authorization' => "GoogleLogin auth=$atoken", # http://code.google.com/intl/bg/apis/gdata/docs/auth/overview.html#ClientLogin
                'role' => 'writer',
                'scope' => 'user',
                'user' => 'new_writer@example.com'
                ],
                $req_content, # content
                [400]
                );
        $req_url = undef;
    } while(defined($req_url));


    foreach (@{$r_lines}) {
        print $_."\n";
    }
}


my $toadd;
$toadd=$ARGV[0];
    
$ua = new LWP::UserAgent;
$ua->timeout(15);
push(@{$ua->requests_redirectable}, 'POST');

$atoken = auth();
add_acls($toadd);

1;
