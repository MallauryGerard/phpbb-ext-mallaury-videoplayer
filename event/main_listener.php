<?php
/**
 *
 * Video Attachment Player. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, mallaury
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mallaury\videoplayer\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * video Attachment Player Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	/**
	 * Video Exts supported, you can add to them but be aware for browser supporting
	 */
	protected $video_ext = array(
		'mp4'	=> 'video/mp4',
		'mov'	=> 'video/quicktime',
		'avi'	=> 'video/x-msvideo',
		'wmv'	=> 'video/x-ms-wmv',
		);

	static public function getSubscribedEvents()
	{
		return array(
			'core.send_file_to_browser_before'				=> 'preserve_video_mime',
			'core.parse_attachments_modify_template_data'	=> 'add_video_attachment',
		);
	}

	/**
	 * adds new array key 'S_VIDEO' when file is supported video ext
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function add_video_attachment($event)
	{
		if (!isset($event['block_array']['S_DENIED']))
		{
			if (array_key_exists($event['attachment']['extension'], $this->video_ext))
			{
				$block_array = $event['block_array'];
				$block_array += array(
					'S_VIDEO'	=> true,
					'U_VIEW_LINK'	=> $event['download_link'] . '&amp;mode=view',
					'MIMETYPE'		=> $this->video_ext[$event['attachment']['extension']],
					);
				unset($block_array['S_FILE']);
				unset($block_array['S_IMAGE']);
				$event['block_array'] = $block_array;
			}
		}
	}

	/**
	 * preserve MIME type for supported video files
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function preserve_video_mime($event)
	{
		if (array_key_exists($event['attachment']['extension'], $this->video_ext))
		{
			$attachment = $event['attachment'];
			$attachment['mimetype'] = $this->video_ext[$event['attachment']['extension']];
			$event['attachment'] = $attachment;
		}
	}

}
