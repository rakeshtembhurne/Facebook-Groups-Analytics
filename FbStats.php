<?php
require 'facebook/facebook.php';

class FbStats {

	public $facebook;
	public $period;
	public $user;
	public $access_token;
	public $permissions;
	public $afterLoginUrl;

	public function __construct($params) {

		// creating facebook instance
		$this->facebook = new Facebook(array(
			'appId' => $params['appId'],
			'secret' => $params['secret'],
			'cookie' => true
		));

		$this->permissions = $params['permissionsArray'];
		$this->afterLoginUrl = $params['afterLoginUrl'];

		$this->logIn();
		$this->askPermissions();

		//$this->user = $this->getUserInfo();
		$this->access_token = $this->facebook->getAccessToken();
	}

	public function askPermissions() {
		try {
			$permissions_list = $this->facebook->api(
				'/me/permissions',
				'GET',
				array(
					'access_token' => $this->access_token
				)
			);
		} catch (FacebookApiException $e) {
            $result = $e->getResult();
			throw new Exception($result['error']['message']);
		}

		//check if the permissions we need have been allowed by the user
		//if not then redirect them again to facebook's permissions page
		$permissions_needed = $this->permissions;
		foreach($permissions_needed as $perm) {
			if( !isset($permissions_list['data'][0][$perm]) || $permissions_list['data'][0][$perm] != 1 ) {
				$login_url_params = array(
					'scope' => implode(',',$this->permissions),
					'fbconnect' =>  1,
					'display'   =>  "page",
					'next' => $this->afterLoginUrl
				);

				$login_url = $this->facebook->getLoginUrl($login_url_params);
				header("Location: {$login_url}");
				exit();
			}
		}
	}

	public function logIn() {
		$this->user = $this->facebook->getUser();

		if ( ! $this->user) {
			$loginUrlParams = array(
				'scope' => explode(',',$this->permissions),
				'fbconnect' =>  1,
				'display'   =>  "page",
				'next' => $this->afterLoginUrl
			);
			$loginUrl = $this->facebook->getLoginUrl($loginUrlParams);

			//redirect to the login URL on facebook
			header("Location: {$loginUrl}");
			exit();
		}
	}

	// source_id, limit, offset, until, since
	public function getFeed($params) {
		if ( ! empty($params)) {
			// source_id
			if (isset($params['source_id'])) {
				$source_id = $params['source_id'];
				unset($params['source_id']);
			} else {
				$source_id = 'me';
			}
			// limit
			if ( ! isset($params['limit'])) {
				$params['limit'] = 50;
			}
			// offset
			if ( ! isset($params['offset'])) {
				$params['offset'] = 0;
			}
			// since
			if ( ! isset($params['since'])) {
				//$params['since'] = 'yesterday';
			}

			// form url parameters
			$urlParams = '?';
			foreach ($params as $key => $value) {
				$urlParams .= "{$key}={$value}&";
			}
		}
		//var_dump($urlParams);exit;
		$data = array();
		try {
			$data = $this->facebook->api("/{$source_id}/feed{$urlParams}");
		} catch (FacebookApiException $e) {
            $result = $e->getResult();
			throw new Exception($result['error']['message']);
		}

		return $data;
	}

	// simple api call
	// 'me' will return your info
	public function getInfo($userId) {
		$data = array();
		try {
			$data = $this->facebook->api("/{$userId}");
		} catch (FacebookApiException $e) {
			$result = $e->getResult();
			throw new Exception($result['error']['message']);
		}

		return $data;
	}

	public function getFeedUsers($feed) {

		$userData = array(
			'id' => null,
			'name' => null,
			'totalStatus' => 0,
			'totalLinks' => 0,
			'totalStatusChars' => 0,
			'didLike' => 0,
			'didComment' => 0,
			'didCommentChars' => 0,
			'gotLikes' => 0,
			'gotComments' => 0,
            'gotLikesOnComments' => 0,
            'gotTags' => 0,
		);
		$users 	= array();

		foreach($feed['data'] as $entry) {

			if (isset($entry['from'])) {
				if ( ! isset($users[$entry['from']['id']])) {
					$users[$entry['from']['id']] = $userData;
					// set name
					$users[$entry['from']['id']]['name'] = $entry['from']['name'];
					// set id
					$users[$entry['from']['id']]['id'] = $entry['from']['id'];
				}
			}
			// status messages
			if ($entry['type'] == 'status') {
				// Add Status count
				$users[$entry['from']['id']]['totalStatus']++;
				// Add total characters of status update
                if (isset($entry['message'])) {
                    //FIXME: It seems posting in marathi or hindi sets isset($entry['message']) to false
                    $users[$entry['from']['id']]['totalStatusChars'] += strlen($entry['message']);
                }

			}

            // links
			if ($entry['type'] == 'link') {
				// Add Status count
				$users[$entry['from']['id']]['totalLinks']++;
			}

            // Likes
            if (isset($entry['likes'])) {
                if (isset($entry['likes']['data'])) {
                    $users[$entry['from']['id']]['gotLikes'] += count($entry['likes']['data']);
                    // loop through likers
                    foreach($entry['likes']['data'] as $like) {
                        // if user is not in the list, add
                        if (!isset($users[$like['id']])) {
                            $users[$like['id']] = $userData;
                            // set name
                            $users[$like['id']]['name'] = $like['name'];
                            // set id
                            $users[$like['id']]['id'] = $like['id'];
                        }
                        // increase didComment
                        $users[$like['id']]['didLike']++;
                    }
                } elseif (isset($entry['likes']['count'])) {
                    $users[$entry['from']['id']]['gotLikes'] += (int)$entry['likes']['count'];
                }
            }

            // Comments
            if (isset($entry['comments'])) {
                if (isset($entry['comments']['data'])) {
                    // increase comment counter
                    $users[$entry['from']['id']]['gotComments'] += count($entry['comments']['data']);
                    // loop through each comment
                    foreach ($entry['comments']['data'] as $comment) {
                        // if this commentator is not in the users list, add this user
                        if (!isset($users[$comment['from']['id']])) {
                            $users[$comment['from']['id']] = $userData;
                            // set name
                            $users[$comment['from']['id']]['name'] = $comment['from']['name'];
                            // set id
                            $users[$comment['from']['id']]['id'] = $comment['from']['id'];
                        }
                        // increase didComment for this user
                        $users[$comment['from']['id']]['didComment']++;
                        // increase didCommentChars
                        $users[$comment['from']['id']]['didCommentChars'] += strlen($comment['message']);
                        // got likes on comments?
                        if (isset($comment['likes'])) {
                            $users[$comment['from']['id']]['gotLikesOnComments'] += (int)$comment['likes'];
                        }
                        // are people tagged in this message?
                        if (isset($comment['message_tags'])) {
                            foreach($comment['message_tags'] as $tag) {
                                // if person being tagged is not in the users list, add this user
                                if (!isset($users[$tag['id']])) {
                                    $users[$tag['id']] = $userData;
                                    // set name
                                    $users[$tag['id']]['name'] = $tag['name'];
                                    // set id
                                    $users[$tag['id']]['id'] = $tag['id'];
                                }
                            }
                            $users[$tag['id']]['gotTags']++;
                        }
                    }
                } elseif (isset($entry['comments']['count'])) {
                    $users[$entry['from']['id']]['gotComments'] += (int)$entry['comments']['count'];
                }
            }
		}

		return $users;
	}

    function getTopUsers($feed, $statName, $count = null) {
        ${$statName} = $name = array();

        $users = $this->getFeedUsers($feed);
        foreach($users as $key => $value) {
            ${$statName}[$key] = $value[$statName];
            $name[$key] = $value['name'];
        }
        array_multisort(${$statName}, SORT_DESC, SORT_NUMERIC, $name, $users);

        if ($count !== null && count($users) > $count) {
            $users = array_slice($users, 0, $count);
        }
        return $users;
    }
}