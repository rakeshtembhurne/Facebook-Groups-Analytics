<!DOCTYPE html>
<?php
require_once '../../FbStats.php';
include_once '../config.php';

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
        <link href="../css/bootstrap.min.css" rel="stylesheet" />
        <link href="../css/custom.css" rel="stylesheet" />
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