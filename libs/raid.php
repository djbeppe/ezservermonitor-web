<?php
require '../autoload.php';
$Config = new Config();

$datas = array();

// if (!(exec('/bin/df -T | awk -v c=`/bin/df -T | grep -bo "Type" | awk -F: \'{print $2}\'` \'{print substr($0,c);}\' | tail -n +2 | awk \'{print $1","$2","$3","$4","$5","$6","$7}\'', $df)))
if (!(exec('cat /proc/mdstat | grep -A1 "^md" | grep -v "\-\-"', $df)))
{
    $datas[] = array(
        'name'   => 'N.A',
        'phisical'  => 'N.A',
        'type'      => 'N.A',
        'status'	=> 'N.A',
        'count'		=> 'N.A',
        'health'	=> 'N.A',
    );
}
else
{
	// print_r($df); // DEBUG

    $raid_devices = array();
    $key = 0;

	for($k = 0; $k<count($df); $k=$k+2)
    {
		$raid_rowA = $df[$k];
		$raid_rowB = $df[$k+1];
        list($raid_name, $raid_info) = explode(':', $raid_rowA);
        list($devices_count, $devices_health) = array_slice(explode(' ', $raid_rowB),-2,2);

		$raid_info = explode(' ',$raid_info);
		$raid_status = $raid_info[1];
		$raid_type = $raid_info[2];
		$raid_phisical = implode(' ',array_slice($raid_info,3));

		/* // DEBUG
		echo $raid_name."\n";
		echo $devices_count."\n";
		echo $devices_health."\n";
		*/

        if (!in_array($raid_name, $raid_devices))
        {
            $raid_devices[] = $raid_name;

            $datas[$key] = array(
				'name'		=> trim($raid_name),
				'phisical'  => trim($raid_phisical),
				'type'      => trim($raid_type),
				'status'	=> trim($raid_status),
				'count'		=> trim($devices_count),
				'health'	=> trim($devices_health),
            );
        }

        $key++;
    }

}

// print_r($datas); // DEBUG
echo json_encode($datas);
