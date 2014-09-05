<?php

/**
 * 
 * Flat WAMP by shevarezo.fr
 * 
 * Version 1.2
 * 
 */


/* ---------------------------- */
/*          PARAMETERS          */
/* ---------------------------- */
$config = array();

$config['version']        = '1.2';
$config['github_url']     = 'https://github.com/ShevAbam/FlatWAMP';
$config['author']         = 'shevarezo.fr';
$config['author_website'] = 'http://www.shevarezo.fr';

$config['title']          = 'Flat WAMP';
$config['wampConfFile']   = '../wampmanager.conf';
$config['dirsToHide']     = array();
$config['colors']         = array('#e74c3c', '#2ecc71', '#3498db', '#9b59b6', '#425b75', '#eabe0f', '#973d00', '#3ebba0', '#d98437', '#de61b9', '#bb4d3e', '#7379cc', '#69cd38', '#8e8fb0');

/* ------------------------ */



/**
 * Sort array by index
 * @param array $records Array of records
 * @param string $field On what field we sort table
 * @param bool $reverse Reverse results
 * @return array
 */
function sortByIndex($records, $field, $reverse = false)
{
    $hash = array();
    
    foreach($records as $record)
        $hash[$record[$field]] = $record;
    
    ($reverse) ? krsort($hash) : ksort($hash);
    
    $records = array();
    
    foreach($hash as $record)
        $records []= $record;
    
    return $records;
}

/**
 * Generate colors randomly
 * @param array $colors Array of hex colors
 * @param int $nb Number of colors to generate
 * @param array $colorsHisto Contains history of the last 8 colors
 * @return array
 */
function generateColor($colors, $nb = 1, $colorsHisto = array())
{
    for ($i = 0; $i < $nb; $i++)
    {
        // 8 colors are kept in memory
        if (count($colorsHisto) == 8)
            $colorsHisto = array_slice($colorsHisto, 1);

        // Diff between full colors array and the last height colors used
        $colors_diff = array_diff($colors, $colorsHisto);
        $colors_diff = array_values($colors_diff);

        // Pick a random color !
        $countColors = count($colors_diff);
        shuffle($colors_diff);
        $random = mt_rand(0, ($countColors-1));

        $new = $colors_diff[$random];

        $colorsHisto[] = $new;
        $results[] = $new;
    }

    return $results;
}


/**
 * Returns server apps names, versions and URLs
 * @param string $file Apache configuration file ($config['apacheConfFile'])
 * @return array
 */
function getServerInfos($file)
{
    if (file_exists($file))
    {
        $content = parse_ini_file($file, true);
        preg_match("([0-9\.]+)", apache_get_version(), $getApache_version);

        $tab = array(
            array(
                'name' => 'WAMP Server',
                'data' => $content['main']['wampserverVersion'],
                'url'  => 'http://www.wampserver.com',
            ),
            array(
                'name' => 'PHP',
                'data' => phpversion(),
                'url'  => 'http://www.php.net',
            ),
            array(
                'name' => 'Apache',
                'data' => $getApache_version[0],
                'url'  => 'http://httpd.apache.org',
            ),
            array(
                'name' => 'MySQL',
                'data' => $content['mysql']['mysqlVersion'],
                'url'  => 'http://www.mysql.com',
            ),
            array(
                'name' => 'PHPMyAdmin',
                'data' => $content['apps']['phpmyadminVersion'],
                'url'  => 'http://www.phpmyadmin.net',
            ),
        );
    }

    return $tab;
}

/**
 * Parses vhost conf and returns its URL
 * @param string Virtual host configuration file path
 * @return string
 */
function getVhostUrl($vhost)
{
    $handle = fopen($vhost, 'r');

    if ($handle)
    {
        while (($buffer = trim(fgets($handle))) !== false)
        {
            if (preg_match('/ServerName (.*)/i', $buffer, $match))
            {
                fclose($handle);
                return $match[1];
            }
        }

        fclose($handle);
    }

    return null;
}

/**
 * Gets virtual hosts names and URLs found in httpd-vhosts.conf file
 * @return array
 */
function getVhosts_httpd_vhosts()
{
    $servers = array();

    $httpd_vhosts = glob('../bin/apache/[Aa]pache*/conf/extra/httpd-vhosts.conf');
    $filename = $httpd_vhosts[0];

    $file = fopen($filename, 'r');

    while (!feof($file))
    {
        $buffer = trim(fgets($file));
        $tokens = explode(' ', $buffer);

        if (!empty($tokens))
        {
            if (strtolower($tokens[0]) == 'servername')
            {
                $servers[] = array(
                    'name' => $tokens[1],
                    'url'  => $tokens[1],
                );
            }
        }
    }

    fclose($file);

    return $servers;
}

/**
 * Gets virtual hosts names and URLs found in ./vhosts folder
 * @return array
 */
function getVhosts_dir_vhosts()
{
    $tab = array();
    $vhosts = glob('../vhosts/*.conf');

    if (count($vhosts) > 0)
    {
        foreach ($vhosts as $vh)
        {
            $url = getVhostUrl($vh);

            $tab[] = array(
                'name' => $url,
                'url'  => $url,
            );
        }
    }

    return $tab;
}

/**
 * Returns array of virtual hosts names and URLs found in www/
 * @return array
 */
function getVhosts_www($to_hide = array())
{
    $return = array();
    $folders = glob('./*', GLOB_ONLYDIR);
    $folders = array_map('basename', $folders);

    foreach ($folders as $folder)
    {
        if (!in_array($folder, $to_hide))
        {
            $return[] = array(
                'name' => $folder,
                'url'  => $_SERVER['HTTP_HOST'].'/'.$folder.'/',
            );
        }
    }

    return $return;
}

/**
 * Returns array of virtual hosts
 * @param array $dirs_to_hide Directories to hide ($config['dirsToHide'])
 * @return array
 */
function getVhosts($dirs_to_hide = array())
{
    $defaults = array();
    $defaults[] = array(
        'name' => 'PHPMyAdmin',
        'url'  => $_SERVER['HTTP_HOST'].'/phpmyadmin',
    );
    $defaults[] = array(
        'name' => 'phpinfo()',
        'url'  => $_SERVER['HTTP_HOST'].'/?phpinfo=1',
    );

    // Vhosts in ./vhosts folder
    $vhosts_1 = getVhosts_dir_vhosts();
    // Vhosts in httpd-vhosts's file
    $vhosts_2 = getVhosts_httpd_vhosts();
    // Directories in /www
    $vhosts_3 = getVhosts_www($dirs_to_hide);

    // We merge those three arrays
    $vhosts_merge = array_merge($vhosts_1, $vhosts_2, $vhosts_3);

    // Sorting by name
    $vhosts = sortByIndex($vhosts_merge, 'name');

    // And finally, add $defaults URLs at the beginning of $vhosts
    return array_merge($defaults, $vhosts);
}


// phpinfo()
if (isset($_GET['phpinfo']))
{
    phpinfo();
    exit;
}

// Retrieving datas
$vhosts      = getVhosts($config['dirsToHide']);
$colors      = generateColor($config['colors'], count($vhosts));
$serverInfos = getServerInfos($config['wampConfFile']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $config['title']; ?></title>
    <link rel="stylesheet" href="flatwamp.css">
    <script src="jquery-2.1.1.min.js"></script>
    <script>
    var o = {
        38: 'up',
        40: 'bottom',
        37: 'prev',
        39: 'next',
        13: 'okay'
    }

    $(document).ready(function(){
        $('.content li').hover(function(){
            $('.content li.active').removeClass('active');
            $(this).toggleClass('active');
        });

        $(document).on('keydown', function(e){
            var dir = o[e.which];
            if (typeof dir == 'undefined')
                return;

            var $active = $('.content li.active');
            var i = $('.content li').index($active);

            if (dir === 'okay')
            {
                var url = $('.content li.active').find('a').attr('href');
                location.href = url;
                return;
            }

            if (!$active.length)
            {
                if (dir === 'next' || dir === 'bottom')
                    $('.content li').first().addClass('active');
                else if (dir === 'prev' || dir === 'up')
                    $('.content li').last().addClass('active');

                return;
            }
            else
            {
                if (dir === 'next' || dir === 'prev')
                {
                    $active.removeClass('active')[dir]().addClass('active');
                }
                else
                {
                    var width = $(window).width();
                    
                    if (width <= 600)
                        var nb_elts = 1;
                    else if (width <= 800)
                        var nb_elts = 2;
                    else
                        var nb_elts = 3;

                    var p = dir === 'up' ? (i - nb_elts) : (i + nb_elts);
                    $('.content li').removeClass('active').eq(p).addClass('active');
                }
            }
            
        });
    });
    </script>
</head>
<body>

<ul class="content">
    <?php foreach ($vhosts as $key => $vhost): ?>
        <li style="background: <?= $colors[$key]; ?>;">
            <a href="http://<?= $vhost['url']; ?>"><?= $vhost['name']; ?></a>
        </li>
    <?php endforeach; ?>
</ul>

<div class="cls"></div>

<footer>
    <ul>
        <?php foreach ($serverInfos as $info): ?>
            <li><a href="<?= $info['url']; ?>"><?= $info['name']; ?></a> : <?= $info['data']; ?></li>
        <?php endforeach; ?>

        <li><a href="<?= $config['github_url']; ?>">Flat WAMP</a> : <?= $config['version']; ?></li>

        <li style="float: right;"><a href="<?= $config['author_website']; ?>"><?= $config['author']; ?></a></li>
    </ul>
</footer>

</body>
</html>