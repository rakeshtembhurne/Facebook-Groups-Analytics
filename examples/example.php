<!DOCTYPE html>
<?php
require_once '../FbStats.php';
include_once 'config.php';

if (!session_id())
    session_start();

function displayUsersTable($users, $column=null) {

    $table = '<table class="bordered-table">';
    foreach ($users as &$user) {
        foreach ($user as $stat => $value) {
            if (in_array($stat, array('id', 'name', $column)) === false) {
                unset($user[$stat]);
            }
        }
    }

    unset($user);

    foreach ($users as $user) {
        $table .= '<tr>';
        foreach ($user as $stat => $value) {
            $table .= ($stat === 'id') ? "<td><img src='https://graph.facebook.com/{$value}/picture' /></td>" : "<td>{$value}</td>";
        }
        $table .= '</tr>';
    }
    $table .= '</table>';

    echo "<div class='span10'><h3>{$column}</h3>{$table}</div>";
}

$fb = new FbStats($config);

$feedParams = array(
    'sourceId' => $sourceId,
    'limit' => 500,
    'since' => 'last+Year',
);
$stats = array(
          'totalStatus',
          'totalLinks',
          'totalStatusChars',
          'didLike',
          'didComment',
          'didCommentChars',
          'gotLikes',
          'gotComments',
          'gotLikesOnComments',
          'gotTags',
         );
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Facebook Groups Analytics</title>
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
        <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!-- Le styles -->
        <link href="bootstrap.min.css" rel="stylesheet">
        <style type="text/css">
            /* Override some defaults */
            html, body {
                background-color: #eee;
            }
            body {
                padding-top: 40px; /* 40px to make the container go all the way to the bottom of the topbar */
            }
            .container > footer p {
                text-align: center; /* center align it with the container */
            }
            .container {
                width: 820px; /* downsize our container to make the content feel a bit tighter and more cohesive. NOTE: this removes two full columns from the grid, meaning you only go to 14 columns and not 16. */
            }

            /* The white background content wrapper */
            .container > .content {
                background-color: #fff;
                padding: 20px;
                margin: 0 -20px; /* negative indent the amount of the padding to maintain the grid system */
                -webkit-border-radius: 0 0 6px 6px;
                -moz-border-radius: 0 0 6px 6px;
                border-radius: 0 0 6px 6px;
                -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.15);
                -moz-box-shadow: 0 1px 2px rgba(0,0,0,.15);
                box-shadow: 0 1px 2px rgba(0,0,0,.15);
            }

            /* Page header tweaks */
            .page-header {
                background-color: #f5f5f5;
                padding: 20px 20px 10px;
                margin: -20px -20px 20px;
            }

            /* Give a quick and non-cross-browser friendly divider */
            .content .span4 {
                margin-left: 0;
                padding-left: 19px;
                border-left: 1px solid #eee;
            }

            .topbar .btn {
                border: 0;
            }

        </style>
    </head>

    <body>

        <div class="topbar">
            <div class="fill">
                <div class="container">
                    <a class="brand" href="#">Facebook Group Analytics</a>
                </div>
            </div>
        </div>

        <div class="container">

            <div class="content">
                <div class="page-header">
                    <h1>Facebook Group Analytics Example</h1>
                </div>
                <div class="row">
                    <div class="span10">
                        <div class="row">
<?php
//get Group Feed
try {
    $groupFeed = $fb->getFeed($feedParams);
} catch (Exception $e) {

    echo '<div class="alert-message error">'.$e->getMessage().'</div>';
}

// totalStatusChars - counts total characters of status updates
foreach($stats as $stat) {
    //var_dump($stat);
    $users = $fb->getTopUsers($groupFeed, $stat, 5);
    displayUsersTable($users, $stat);
}
?>
                        </div>
                    </div>
                    <div class="span4">
                        <h3>Project Links</h3>
                        <ul>
                            <li><a href="https://github.com/rakeshtembhurne/Facebook-Groups-Analytics">Facebook Groups Analytics on Github</a></li>
                            <li><a href="https://www.facebook.com/groups/nagpurpug/">Nagpur PHP Users Group</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <footer>
                <p>&copy; Company 2011</p>
            </footer>

        </div> <!-- /container -->

    </body>
</html>