<?php
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

$messages = array(
 'totalStatus'        => 'Top status updators',
 'totalLinks'         => 'Top link sharer',
 'totalStatusChars'   => 'Longest status message writer',
 'didLike'            => 'Top likers',
 'didComment'         => 'Top commentators',
 'didCommentChars'    => 'Longest comments writers',
 'gotLikes'           => 'Top liked for status messages',
 'gotComments'        => 'Top Comments Receivers',
 'gotLikesOnComments' => 'Top liked for comments',
 'gotTags'            => 'Top tagged person',
);

$units = array(
 'totalStatus'        => 'status updates',
 'totalLinks'         => 'links',
 'totalStatusChars'   => 'characters',
 'didLike'            => 'likes',
 'didComment'         => 'comments',
 'didCommentChars'    => 'characters',
 'gotLikes'           => 'likes',
 'gotComments'        => 'comments',
 'gotLikesOnComments' => 'likes',
 'gotTags'            => 'tags',
);

$period = array(
 '-1 days'   => 'Yesterday',
 '-2 days'   => 'Last 2 days',
 '-1 weeks'  => 'Last week',
 '-2 weeks'  => 'Last 2 weeks',
 '-1 months' => 'Last month',
 '-2 months' => 'Last 2 months',
 '-3 months' => 'Last 3 months',
 '-6 months' => 'Last 6 months',
);

function getUsersTable($users, $column=null) {

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

   return $table;
}
?>