<?php
namespace PublishPress\Capabilities;

/**
 * PublishPress\Capabilities\WP_REST_Workarounds class
 *
 * @author Kevin Behrens
 * @copyright Copyright (c) 2019, PublishPress
 * @link https://publishpress.com
 *
 */
class WP_REST_Workarounds
{
	var $post_id = 0;
	var $is_posts_request = false;

	function __construct() {
		add_filter('rest_pre_dispatch', [$this, 'fltRestPreDispatch'], 10, 3);
		add_filter('user_has_cap', [$this, 'fltPublishCapReplacement'], 5, 3);

		add_filter('pre_post_status', [$this, 'fltPostStatus'], 10, 1);
	}

    /**
    * Work around Gutenberg editor enforcing publish_posts capability instead of edit_published_posts.
    * 
    * Allow edit_published capability to satisfy publish capability requirement if:
	*   - The query pertains to a specific post
	*	- The post type and its capabilities are defined and match the current publish capability requirement
	*	- The post is already published with a public status, or scheduled
    *
	* @author Kevin Behrens
	* @link   https://core.trac.wordpress.org/ticket/47443
	* @link   https://github.com/WordPress/gutenberg/issues/13342
    * @param  array  $wp_sitecaps  Array of user capabilities acknowledged for this request.
    * @param  array  $reqd_caps    Capability requirements
    * @param  array  $args         Additional arguments passed into user_has_cap filter
    */
	public function fltPublishCapReplacement($wp_sitecaps, $reqd_caps, $args)
	{
		if ($reqd_cap = reset($reqd_caps)) {
			// slight compromise for perf: apply this workaround only when publish_posts capability for post type follows typical pattern (publish_*)
			if (0 === strpos($reqd_cap, 'publish_')) { 
				if (!empty($wp_sitecaps[$reqd_cap])) {
					return $wp_sitecaps;
				}

				if (!$_post = get_post($this->getPostID())) {
					return $wp_sitecaps;
				}
				
				$type_obj = get_post_type_object($_post->post_type);
				$status_obj = get_post_status_object($_post->post_status);

				if ($type_obj && !empty($type_obj->cap) 
				&& !empty($type_obj->cap->publish_posts) && !empty($type_obj->cap->edit_published_posts)
				&& $type_obj->cap->publish_posts == $reqd_cap
				&& $status_obj && (!empty($status_obj->public) || 'future' == $_post->post_status)
				) {
					if (!empty($wp_sitecaps[$type_obj->cap->edit_published_posts])) {
						$wp_sitecaps[$reqd_cap] = true;
					}
				}
			}
		}

		return $wp_sitecaps;
	}

	/**
	* If the post is already published, prevent the workaround from allowing status to be changed via "Switch to Draft" (or by any other means).
	*
	* This will also prevent users with edit_published capability but not publish capability from unpublishing via Quick Edit.
    *
    * @param  string  $post_status	New post status about to be saved
    */
	function fltPostStatus($post_status) {
		global $current_user;
		
		if ($_post = get_post($this->getPostID())) {
			$type_obj = get_post_type_object($_post->post_type);
			$status_obj = get_post_status_object($_post->post_status);

			if ($type_obj && $status_obj && (!empty($status_obj->public) || !empty($status_obj->private) || 'future' == $_post->post_status)) {
				if (empty($current_user->allcaps[$type_obj->cap->publish_posts])) {
					$post_status = $_post->post_status;
				}
			}
		}

		return $post_status;
	}

	private function getPostID()
    {
        global $post;

        if (defined('REST_REQUEST') && REST_REQUEST && $this->is_posts_request) {
            return $this->post_id;
        }

        if (!empty($post) && is_object($post)) {
            return ('auto-draft' == $post->post_status) ? 0 : $post->ID;
		} elseif (isset($_REQUEST['post'])) {
            return (int)$_REQUEST['post'];
        } elseif (isset($_REQUEST['post_ID'])) {
            return (int)$_REQUEST['post_ID'];
        } elseif (isset($_REQUEST['post_id'])) {
            return (int)$_REQUEST['post_id'];
        }
	}
		
	public function fltRestPreDispatch($rest_response, $rest_server, $request)
	{
		$method = $request->get_method();
		$path = $request->get_route();
		
		foreach ($rest_server->get_routes() as $route => $handlers) {
			if (!$match = preg_match( '@^' . $route . '$@i', $path, $matches )) {
				continue;
			}

			$args = [];
			foreach ($matches as $param => $value) {
				if (!is_int($param)) {
					$args[ $param ] = $value;
				}
			}

			foreach ($handlers as $handler) {
				if (is_array($handler['callback']) && isset($handler['callback'][0]) && is_object($handler['callback'][0])
				&& 'WP_REST_Posts_Controller' == get_class($handler['callback'][0])
				) {
					if ( ! $this->post_id = (!empty($args['id'])) ? $args['id'] : 0) {
						$this->post_id = (!empty($this->params['id'])) ? $this->params['id'] : 0;
					}

					$this->is_posts_request = true;
					break 2;
				}
			}
		}

		return $rest_response;
	} 
}
