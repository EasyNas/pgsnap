<?php

/*
 * Copyright (c) 2008-2016 Guillaume Lelarge <guillaume@lelarge.info>
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

$buffer = $navigate_globalobjects.'
<div id="pgContentWrap">

<h1>Total Objects Per Tablespace</h1>
';


$query = "SELECT
  pg_get_userbyid(spcowner) AS rolname,
  spcname,
  CASE WHEN relkind='r' THEN 'Tables' WHEN relkind='i' THEN 'Index' ELSE 'Materialized views' END AS kind,
  count(*) AS total
FROM pg_class, pg_tablespace
WHERE pg_tablespace.oid=reltablespace
  AND relkind IN ('r', 'i', 'm')
GROUP BY 1, 2, 3
ORDER BY 1, 2, 3;";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst" width="20%">Tablespace Owner</th>
  <th class="colMid" width="20%">Tablespace Name</th>
  <th class="colMid" width="20%">Object\'s type</th>
  <th class="colLast" width="20%">Count</th>
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
  $buffer .= tr().'
  <td title="'.$comments['roles'][$row['rolname']].'">'.$row['rolname'].'</td>
  <td title="'.$comments['tablespaces'][$row['spcname']].'">'.$row['spcname']."</td>
  <td>".$row['kind']."</td>
  <td>".$row['total']."</td>
</tr>";
}
$buffer .= '</tbody>
</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/tblspc1.html';
include 'lib/fileoperations.php';

?>
