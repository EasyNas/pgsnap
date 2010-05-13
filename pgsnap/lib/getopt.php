<?php

/*
 * Copyright (c) 2008-2010 Guillaume Lelarge <guillaume@lelarge.info>
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */

$VERSION = '0.5.0';

$PGHOST = getenv('PGHOST');

$PGPORT = getenv('PGPORT');

$PGUSER = getenv('PGUSER');
if (strlen("$PGUSER") == 0) {
  $PGUSER = getenv('USER');
}

$PGDATABASE = getenv('PGDATABASE');
if (strlen("$PGDATABASE") == 0) {
  $PGDATABASE = $PGUSER;
}

$PGPASSWORD = getenv('PGPASSWORD');
$g_passwordrequired = false;
$g_nopassword = false;
$g_withoutsysobjects = false;
$g_alldatabases = false;
$g_deleteifexists = false;
$g_witholdlibpq = false;
$outputdir = '';
$queriesinlogs = false;

for ($i = 1; $i < $_SERVER["argc"]; $i++) {
  switch($_SERVER["argv"][$i]) {
    case "-v":
    case "--version":
      echo  $_SERVER['argv'][0]." $VERSION\n";
      exit;
      break;
    case "-h":
    case "--host":
      $PGHOST = $_SERVER['argv'][++$i];
      break;
    case "-p":
    case "--port":
      $PGPORT = $_SERVER['argv'][++$i];
      break;
    case "-U":
    case "--user":
      $PGUSER = $_SERVER['argv'][++$i];
      break;
    case "-d":
    case "--database":
      $PGDATABASE = $_SERVER['argv'][++$i];
      break;
    case "-w":
    case "--no-password":
      if ($g_passwordrequired) {
        die("-w and -W parameters are mutually exclusive.\n");
      }
      $g_nopassword = true;
      break;
    case "-W":
      if ($g_nopassword) {
        die("-w and -W parameters are mutually exclusive.\n");
      }
      $g_passwordrequired = true;
      break;
    case "--with-old-libpq":
      $g_witholdlibpq = true;
      break;
    case "-o":
    case "--output-dir":
      if ($g_alldatabases) {
        die("-a and -o parameters are mutually exclusive.\n");
      }
      $outputdir = $_SERVER['argv'][++$i];
      break;
    case "-S":
    case "--without-sysobjects":
      $g_withoutsysobjects = true;
      break;
    case "-a":
    case "--all":
      if (strlen($outputdir) > 0) {
        die("-a and -o parameters are mutually exclusive.\n");
      }
      $g_alldatabases = true;
      break;
    case "--delete-if-exists":
      $g_deleteifexists = true;
      break;
    case "--query-in-logs":
      $queriesinlogs = true;
      break;
    case "-?":
    case "-h":
    case "--help":
?>
This is <?= $_SERVER['argv'][0]; ?> <?= $VERSION ?>.

Usage:
  <?= $_SERVER['argv'][0]; ?> [OPTIONS]... [DBNAME]

General options:
  -a, --all       build a report for all databases on the PostgreSQL server
  -d DBNAME       specify database name to connect to
                  (default: "<?= $PGDATABASE ?>")
  -o outputdir    specify output directory
                  (default: "<?= $outputdir ?>")
  --with-old-libpq
                  disable the use of the parameter application_name
  -S, --without-sysobjects
                  get report without system objects informations
  --delete-if-exists
                  delete output directory if it already exists
  --help          show this help, then exit
  --version       output version information, then exit

Connection options:
  -h HOSTNAME     database server host or socket directory
                  (default: "<?= $PGHOST ?>")
  -p PORT         database server port (default: "<?= $PGPORT ?>")
  -U NAME         database user name (default: "<?= $PGUSER ?>")
  -W              prompt for password
  -w              don't prompt for password

<?php
      exit;
      break;
    default:
      $PGDATABASE = $_SERVER['argv'][$i];
      break;
  }
}

?>
