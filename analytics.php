<?php

$domain = 'wanotify.uw.edu';
$show = array(
"iOS installs",
"/",
"/uw",
"/FAQ",
"/privacy-policy",
"/next-steps-count",
// "/next-steps",
// "/uw-next-steps",
// "/wanotify-ios-install.mobileconfig",
// "/robots.txt",
);
$combine = array(
    "/cc-exposures-logo-green.png" => array("iOS installs"),
    "/wa-en-banner-green.png" => array("iOS installs")
);
$start_date = date_create_from_format('Y-m-d', '2020-11-01');
$end_date = date_create_from_format('Y-m-d', null);
$site_interval = 'daily';
$resource_interval = 'hourly'; // daily or hourly

// Append archived log files and current
$log_files = explode("\n", shell_exec("find /var/log/apache2/2020/11 -maxdepth 1 -iname 'ssl-wanotify*_access*'"));
$log_files2 = explode("\n", shell_exec("find /var/log/apache2/2020/12 -maxdepth 1 -iname 'ssl-wanotify*_access*'"));
$log_files3 = explode("\n", shell_exec("find /var/log/apache2 -maxdepth 1 -iname 'ssl-wanotify*_access*'"));
$log_files = array_merge($log_files, $log_files2, $log_files3);

$one_month = new DateInterval('P1M');
$curr_date = $start_date;
$log_files = array();

// find params
$log_base_folder = "/var/log/apache2";
$max_depth = 1;
$search_string = "ssl-wanotify*_access*";
// get access log archives
while ($curr_date <= $end_date){
    $log_files = array_merge($log_files, explode("\n", shell_exec("find $log_base_folder/{$curr_date->format('Y')}/{$curr_date->format('m')} -maxdepth $max_depth -iname '$search_string'")));
    $curr_date = $curr_date + $one_month;
}
$log_files = array_merge($log_files, explode("\n", shell_exec("find $log_base_folder -maxdepth $max_depth -iname '$search_string'")));
// // $log_files = array("./ssl-test.log"); // test
array_multisort($log_files);
// $log_arr = array();
// foreach ($log_files as $filename) {
//     if ($filename) {
//         echo "$filename\n";
//         $contents;
//         if (strpos($filename, ".gz") !==FALSE) {
//             $contents = gzfile($filename);
//         } else {
//             $contents = file($filename);
//         }
//         if ($contents) {
//             $log_arr = array_merge($log_arr, $contents);
//         }
//     }
// }
$log_file = "/var/log/apache2/ssl-".$domain."_access_ssl.log";
$log_arr = file($log_file);
//$log_arr = gzfile('/var/log/apache2/2020/11/ssl-wanotify.cirg.washington.edu_access_ssl.log-20201108.gz');//ssl-wanotify.cirg.washington.edu_access_ssl.log');
//$log_arr = file('./ssl-test.log');

$tz = 'US/Pacific';
$timestamp = time();
$dt = new DateTime('now', new DateTimeZone($tz));
$dt->setTimestamp($timestamp);
$timestamp = $dt->format('Y-m-d H:i:s');
$output_folder = "./analytics_output";
if (!file_exists($output_folder)) {
    mkdir($output_folder, 0777, true);
}
$txt_out = "$output_folder/analytics_$timestamp.txt";
$txt_handle = fopen($txt_out, 'w');
echo "Text output: $txt_out\n";

$astring = join("", $log_arr);
$astring = preg_replace("/(\r|\t)/", "", $astring);
$records = preg_split("/\n/", $astring, -1, PREG_SPLIT_NO_EMPTY);
$sizerecs = sizeof($records);

$resources = array();

 /*
 Visits to .html/no-extension resources
 count of users = # unique visitors
 [
    [
        'date' => yyyy-mm-dd,
        'visitors' => [],
        'views' => #,
        'hits' => #,
    ], ...
 ]
 */
$site_stats = array();

/*
 Hits on all resources and status codes
 [
    'page' => [
        'date' => yyyy-mm-dd,
        'hour' => hh:ii,
        'visitors' => [],
        'hits' => #,
    ], ...
 ]
 */
$resource_stats = array();

$start_time = time();

foreach ($log_files as $filename) {
    if ($filename) {
        echo "$filename\n";
        $isgz = strpos($filename, ".gz") !==false;
        $handle;
        $content;
        if ($isgz) {
            $handle = gzopen($filename, "r");
            $content = gzgets($handle,4096);
        } else {
            $handle = fopen($filename, "r");
            $content = fgets($handle,4096);
        }

        while ($content !== false) {
            $debug = false;
            // fwrite($txt_handle, "$content\n");
            // IP address
            preg_match("/([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+) - - /", $content, $match); // '123.123.123.123 - - '
            $ip = $match[1] ?? "";
            $content = str_replace($match[0], "", $content);
            
            // fwrite($txt_handle, "$content\n");
            // timestamp
            preg_match("/\[(.+?)\]/", $content, $match);
            $access_time = $match[1] ?? "";
            preg_match("/(\d{2})\/([A-Za-z]{3})\/(\d{4})/", $access_time, $date_match); // '19/Oct/2020'
            $date = $date_match[0] ?? "";
            preg_match("/(\d{2}):(\d{2}):(\d{2}).(\d{3})/", $access_time, $time_match); // 
            $hour = $time_match[1] ?? "";
            $content = str_replace($match[0], "", $content);
            
            // fwrite($txt_handle, "$content\n");
            // resource path
            preg_match("/\"[A-Z]{3,8} (.[^\"]+)\"/", $content, $match);
            $http = $match[1] ?? "";
            $parts = explode(" ", $http);
            $url = $parts[0];
            $page = page_basename($url);
            $content = str_replace($match[0], "", $content);
            
            // fwrite($txt_handle, "$content\n");
            // status code
            preg_match("/([0-9]{3})/", $content, $match);
            $status_code = $match[1] ?? "";
            $content = str_replace($match[0], "", $content);
            
            // fwrite($txt_handle, "$content\n");
            // referring url
            preg_match("/\"([^\"]+)\"/", $content, $match);
            $ref = $match[1] ?? "";
            $content = str_replace($match[0], "", $content);
            
            // fwrite($txt_handle, "$content\n");
            // browser
            preg_match("/\"([^\"]+)\"/", $content, $match);
            if ($match) {
                $browser = $match[1] ?? "";
                preg_match("/^[^\/]*/", $browser, $browser_match);
                $user_agent = $browser_match[0] ?? "";
                if (!$user_agent) {$debug = true;}
                $content = str_replace($match[0], "", $content);
            }
            
            // fwrite($txt_handle, "$content\n");
            // bytes transferred
            // skip if no bytes sometimes prematurely removed from str
            preg_match("/([0-9]+\b|-)/", $content, $match);
            $bytes = 0;
            if ($match) {
                if (isset($match[1]) && $match[1] != "-") {
                    $bytes = $match[1];
                }
                $content = str_replace($match[0], "", $content);
            }

            if (date_in_range($date, $start_date)) {
                $aliases = array($page);
                if (isset($combine[$page])) {
                    $aliases = array_merge($aliases, $combine[$page]);
                }

                foreach ($aliases as $key) {
                    if (isset($resources[$key][$date][$ip][$status_code])) {
                        $resources[$key][$date][$ip][$status_code]++;
                    } else {
                        $resources[$key][$date][$ip][$status_code] = 1;
                    }

                    if (!isset($site_stats[$date])) {
                        $site_stats[$date] = array(
                            'time' => $date,
                            'hits' => 0,
                            'views' => 0,
                            'visitors' => array(),
                        );
                    }
                    $resource_ext = get_extension($key);
                    if ($status_code < 400 && (!$resource_ext || $resource_ext == 'html')) {
                        $site_stats[$date]['visitors'][$ip] = 1;
                        $site_stats[$date]['views']++;
                    }
                    $site_stats[$date]['hits']++;

                    if (!$show || in_array($key, $show)) {
                        if ($key == 'iOS installs' && $user_agent) {
                            $key .= " $user_agent";
                        }
                        $time_key = $date;
                        if ($resource_interval == 'hourly') {
                            $time_key .= " $hour";
                        }
                        if (!isset($resource_stats[$key][$time_key])) {
                            $resource_stats[$key][$time_key] = array(
                                'resource' => $key,
                                'time' => $time_key,
                                'hits' => 0,
                                'visitors' => array(),
                                // status codes added below
                            );
                        }
                        if (!isset($resource_stats[$key][$time_key][$status_code])){
                            $resource_stats[$key][$time_key][$status_code] = 0;
                        }
                        $resource_stats[$key][$time_key][$status_code]++;

                        $resource_stats[$key][$time_key]['visitors'][$ip] = 1;

                        $resource_stats[$key][$time_key]['hits']++;
                    }
                }
            }
            
            if ($debug) {
                fwrite($txt_handle, "Row: i\n$content\nIP: $ip\nTime: $access_time\nPage: $page\nType: link[1]\nCode: $status_code\nBytes: $bytes\nRef: $ref\nBrowser: $browser");
            }

            if ($isgz) {
                $content = gzgets($handle,4096);
            } else {
                $content = fgets($handle,4096);
            }
        }
        
        if ($isgz) {
            gzclose($handle);
        } else {
            fclose($handle);
        }
    }
}
// foreach($records as $all) {
//     $debug = false;
//     // fwrite($txt_handle, "$all\n");
//     // IP address
//     preg_match("/([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+) - - /", $all, $match); // '123.123.123.123 - - '
//     $ip = $match[1] ?? "";
//     $all = str_replace($match[0], "", $all);
    
//     // fwrite($txt_handle, "$all\n");
//     // timestamp
//     preg_match("/\[(.+?)\]/", $all, $match);
//     $access_time = $match[1] ?? "";
//     preg_match("/(\d{2})\/([A-Za-z]{3})\/(\d{4})/", $access_time, $date_match); // '19/Oct/2020'
//     $date = $date_match[0] ?? "";
//     preg_match("/(\d{2}):(\d{2}):(\d{2}).(\d{3})/", $access_time, $time_match); // 
//     $hour = $time_match[1] ?? "";
//     $all = str_replace($match[0], "", $all);
    
//     // fwrite($txt_handle, "$all\n");
//     // resource path
//     preg_match("/\"[A-Z]{3,8} (.[^\"]+)\"/", $all, $match);
//     $http = $match[1] ?? "";
//     $parts = explode(" ", $http);
//     $url = $parts[0];
//     $page = page_basename($url);
//     $all = str_replace($match[0], "", $all);
    
//     // fwrite($txt_handle, "$all\n");
//     // status code
//     preg_match("/([0-9]{3})/", $all, $match);
//     $status_code = $match[1] ?? "";
//     $all = str_replace($match[0], "", $all);
    
//     // fwrite($txt_handle, "$all\n");
//     // referring url
//     preg_match("/\"([^\"]+)\"/", $all, $match);
//     $ref = $match[1] ?? "";
//     $all = str_replace($match[0], "", $all);
    
//     // fwrite($txt_handle, "$all\n");
//     // browser
//     preg_match("/\"([^\"]+)\"/", $all, $match);
//     if ($match) {
//         $browser = $match[1] ?? "";
//         preg_match("/^[^\/]*/", $browser, $browser_match);
//         $user_agent = $browser_match[0] ?? "";
//         if (!$user_agent) {$debug = true;}
//         $all = str_replace($match[0], "", $all);
//     }
    
//     // fwrite($txt_handle, "$all\n");
//     // bytes transferred
//     // skip if no bytes sometimes prematurely removed from str
//     preg_match("/([0-9]+\b|-)/", $all, $match);
//     $bytes = 0;
//     if ($match) {
//         if (isset($match[1]) && $match[1] != "-") {
//             $bytes = $match[1];
//         }
//         $all = str_replace($match[0], "", $all);
//     }

//     if (date_in_range($date, $start_date)) {
//         $aliases = array($page);
//         if (isset($combine[$page])) {
//             $aliases = array_merge($aliases, $combine[$page]);
//         }

//         foreach ($aliases as $key) {
//             if (isset($resources[$key][$date][$ip][$status_code])) {
//                 $resources[$key][$date][$ip][$status_code]++;
//             } else {
//                 $resources[$key][$date][$ip][$status_code] = 1;
//             }

//             if (!isset($site_stats[$date])) {
//                 $site_stats[$date] = array(
//                     'time' => $date,
//                     'hits' => 0,
//                     'views' => 0,
//                     'visitors' => array(),
//                 );
//             }
//             $resource_ext = get_extension($key);
//             if ($status_code < 400 && (!$resource_ext || $resource_ext == 'html')) {
//                 $site_stats[$date]['visitors'][$ip] = 1;
//                 $site_stats[$date]['views']++;
//             }
//             $site_stats[$date]['hits']++;

//             if (!$show || in_array($key, $show)) {
//                 if ($key == 'iOS installs' && $user_agent) {
//                     $key .= " $user_agent";
//                 }
//                 $time_key = $date;
//                 if ($resource_interval == 'hourly') {
//                     $time_key .= " $hour";
//                 }
//                 if (!isset($resource_stats[$key][$time_key])) {
//                     $resource_stats[$key][$time_key] = array(
//                         'resource' => $key,
//                         'time' => $time_key,
//                         'hits' => 0,
//                         'visitors' => array(),
//                         // status codes added below
//                     );
//                 }
//                 if (!isset($resource_stats[$key][$time_key][$status_code])){
//                     $resource_stats[$key][$time_key][$status_code] = 0;
//                 }
//                 $resource_stats[$key][$time_key][$status_code]++;

//                 $resource_stats[$key][$time_key]['visitors'][$ip] = 1;

//                 $resource_stats[$key][$time_key]['hits']++;
//             }
//         }
//     }
    
//     if ($debug) {
//         fwrite($txt_handle, "Row: i\n$all\nIP: $ip\nTime: $access_time\nPage: $page\nType: link[1]\nCode: $status_code\nBytes: $bytes\nRef: $ref\nBrowser: $browser");
//     }
// }

//==== d3 Outputs ===============================

// Format and flatten daily site stats for d3
$site_out = "./site_stats_$site_interval.json";
$site_handle = fopen($site_out, 'w');
echo "Site data: $site_out\n";
foreach($site_stats as $day => $stats) {
    $site_stats[$day]['visitors'] = sizeof($stats['visitors']);
}
$site_stats = array_values($site_stats);
fwrite($site_handle, json_encode($site_stats, JSON_PRETTY_PRINT));

// Format and flatten hourly resource stats for d3
$resource_stats_flat = array();
foreach($resource_stats as $resource => $time_stats) {
    foreach($time_stats as $time => $stats) {
        $resource_stats[$resource][$time]['visitors'] = sizeof($stats['visitors']);
    }
    $resource_stats_flat = array_merge($resource_stats_flat, array_values($resource_stats[$resource]));
}
$resource_out = "./resource_stats_$resource_interval.json";
$resource_handle = fopen($resource_out, 'w');
echo "Resource data: $resource_out\n";
fwrite($resource_handle, json_encode($resource_stats_flat, JSON_PRETTY_PRINT));

//===============================================

//=== log output ================================

// Write complete daily counts to timestamped file
foreach($resources as $page => $dates) {
    fwrite($txt_handle, "Page: $page\n");
    foreach($dates as $date => $ips) {
        fwrite($txt_handle, "\tDate: $date\n");
        $hit_total = 0;
        $codecount = array();
        foreach($ips as $ip => $status_codes) {
            foreach($status_codes as $code => $count) {
                $hit_total += $count;
                if (!isset($codecount[$code])) {$codecount[$code] = 0;}
                $codecount[$code] += $count;
            }
        }
        $unique = sizeof($ips);
        fwrite($txt_handle, "\tUnique Visitors: $unique\n");
        fwrite($txt_handle, "\tHits: $hit_total\n");
        fwrite($txt_handle, "\tStatus Codes:\n");
        foreach ($codecount as $code => $count) {
            fwrite($txt_handle, "\t\t$code: $count\n");
        }
    }
    fwrite($txt_handle, "\n");
}

//===============================================

$end_dt = new DateTime('now', new DateTimeZone($tz));
echo "End time: {$end_dt->format('Y-m-d H:i:s')}\n";
fclose($txt_handle);
fclose($site_handle);
fclose($resource_handle);

//=== function defs =============================
function date_in_range($date_str, $start_date=null, $stop_date=null) {
    $date = date_create_from_format('d/M/Y', $date_str);
    $track = !(isset($start_date) && $date < $start_date || isset($stop_date) && $date > $stop_date);
    return $track;
}

function page_basename($url) {
    preg_match("/^[^\?]*/", $url, $match);
    $no_params = $match[0] ?? "";
    if (strpos($no_params, ".html")) {
        $no_params = substr_replace($no_params, "", -5);
    }
    if ($no_params == "/index") {
        $no_params = "/";
    }
    return $no_params;
}

function get_extension($url) {
    preg_match("/.*\.(.+?)$/", $url, $match);
    if (isset($match[1])) {
        return strtolower($match[1]);
    }
    return false;
}
?>
