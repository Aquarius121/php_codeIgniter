<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Content extends Model {
	
	const MAX_SLUG_LENGTH = 80;
	
	const TYPE_PR              = 'pr';
	const TYPE_NEWS            = 'news';
	const TYPE_IMAGE           = 'image';
	const TYPE_AUDIO           = 'audio';
	const TYPE_VIDEO           = 'video';
	const TYPE_EVENT           = 'event';
	const TYPE_BLOG            = 'blog';
	const TYPE_SOCIAL          = 'social';

	// Google News URI support was 
	// implemented at this ID.
	// All future PR must conform.
	const GNEWS_MIN_ID = 6778713;

	public $id;
	public $company_id;
	public $type;
	public $slug;
	public $date_created;
	public $date_publish;
	public $is_published;
	public $is_draft;
	
	const BASIC    = 'BASIC';
	const PREMIUM  = 'PREMIUM';
	
	const DIST_BASIC = Model_Content_Distribution_Bundle::DIST_BASIC;
	const DIST_PREMIUM = Model_Content_Distribution_Bundle::DIST_PREMIUM;
	const DIST_PREMIUM_FINANCIAL = Model_Content_Distribution_Bundle::DIST_PREMIUM_FINANCIAL;
	const DIST_PREMIUM_PLUS = Model_Content_Distribution_Bundle::DIST_PREMIUM_PLUS;
	const DIST_PREMIUM_PLUS_NATIONAL = Model_Content_Distribution_Bundle::DIST_PREMIUM_PLUS_NATIONAL;
	const DIST_PREMIUM_PLUS_STATE = Model_Content_Distribution_Bundle::DIST_PREMIUM_PLUS_STATE;

	const DEFAULT_TITLE = 'Untitled';
	const DEFAULT_TITLE_PATTERN = '/^Untitled \([a-f0-9]+\)$/i';
	const DEFAULT_TITLE_FORMAT = 'Untitled (%s)';

	protected $m_newsroom = null;
	protected $m_user = null;
	protected $m_content_distribution_bundle = null;
	
	protected static $__table = 'nr_content';
	protected static $__compressed = array('content');
	protected $__has_loaded_local_data = false;
	protected $__has_loaded_content_data = false;

	// types that require approval
	protected static $__requires_approval = array(
		Model_Content::TYPE_PR,
	);
	
	// types that support credits
	protected static $__requires_credit = array(
		Model_Content::TYPE_PR,
	);

	public static function __requires_credit()
	{
		return static::$__requires_credit;
	}
	
	public static function find_slug($slug)
	{
		return static::find('slug', $slug);
	}

	public static function find_uuid($uuid)
	{
		return static::find('uuid', $uuid);
	}
		
	public static function recent_tags($company, $limit)
	{
		$limit = (int) $limit;
		$sql = "SELECT t.value 
			FROM nr_content c 
			INNER JOIN nr_content_tag t
			ON c.id = t.content_id AND c.company_id = ?
			GROUP BY t.value 
			ORDER BY c.id DESC, t.value ASC 
			LIMIT {$limit}";
		
		$results = array();
		$model = new static();
		$query = $model->db->query($sql, array($company));
		
		foreach ($query->result() as $result)
			$results[] = $result->value;
		
		return $results;
	}

	public function distribution_bundle()
	{
		if (!$this->m_content_distribution_bundle)
		{
			$bundle = Model_Content_Distribution_Bundle::find($this->id);
			$this->m_content_distribution_bundle = $bundle;
		}

		if (!$this->m_content_distribution_bundle)
		{
			$bundle = new Model_Content_Distribution_Bundle();
			$this->m_content_distribution_bundle = $bundle;
			$bundle->content_id = $this->id;
			$bundle->bundle = $this->is_premium 
				? static::DIST_PREMIUM
				: static::DIST_BASIC;
		}

		return $this->m_content_distribution_bundle;
	}
	
	public function set_tags($tags)
	{
		$this->db->query("DELETE FROM nr_content_tag 
			WHERE content_id = ?", array($this->id));
		
		foreach ($tags as $tag)
		{
			if (!($tag = trim($tag))) continue;
			$uniform = Tag::uniform($tag);
			$this->db->query("INSERT IGNORE INTO nr_content_tag (content_id, 
				value, uniform) VALUES (?, ?, ?)", array($this->id, $tag, $uniform));
		}
	}

	public function set_beats($beats)
	{
		$this->db->query("DELETE FROM nr_beat_x_content
			WHERE content_id = ?", array($this->id));
		
		foreach ($beats as $beat)
		{
			if (!$beat) continue;
			if ($beat instanceof Model_Beat) $beat = $beat->id;
			$this->db->query("INSERT IGNORE INTO nr_beat_x_content (beat_id, 
				content_id) VALUES (?, ?)", array($beat, $this->id));
		}
	}
	
	public function set_images($images)
	{
		$this->db->query("DELETE FROM nr_content_image
			WHERE content_id = ?", array($this->id));
		
		foreach ($images as $image)
		{
			if (!$image) continue;
			if ($image instanceof Model_Image) $image = $image->id;
			$this->db->query("INSERT IGNORE INTO nr_content_image (content_id, 
				image_id) VALUES (?, ?)", array($this->id, $image));
		}
	}
	
	public function get_tags()
	{
		$tags = array();
		$query = $this->db->query("SELECT value FROM nr_content_tag 
			WHERE content_id = ?", array($this->id));
		
		foreach ($query->result() as $result)
			$tags[] = $result->value;
		
		return $tags;
	}
	
	public function get_tags_string()
	{
		$tags = $this->get_tags();
		return comma_separate($tags, true);
	}
	
	public function get_images()
	{
		$query = $this->db->query("SELECT i.* FROM nr_image i 
			INNER JOIN nr_content_image ci 
			ON i.id = ci.image_id
			WHERE ci.content_id = ?", 
			array($this->id));
		
		$images = Model_Image::from_db_all($query);
		return $images;
	}

	public function get_beats()
	{
		$query = $this->db->query("SELECT b.* FROM nr_beat b
			INNER JOIN nr_beat_x_content bxc
			ON b.id = bxc.beat_id
			WHERE bxc.content_id = ?", 
			array($this->id));
		
		$beats = Model_Beat::from_db_all($query);
		return $beats;
	}

	public function reload()
	{
		parent::reload();

		if ($this->__has_loaded_local_data)
		{
			$this->__has_loaded_local_data = false;
			$this->load_local_data();
		}

		if ($this->__has_loaded_content_data)
		{
			$this->__has_loaded_content_data = false;
			$this->load_content_data();
		}
	}

	public function clear_cached_data()
	{
		$this->__has_loaded_content_data = false;
		$this->__has_loaded_local_data = false;
	}

	public function load_local_data()
	{
		if (!$this->type) return;
		if ($this->__has_loaded_local_data) return;
		$this->__has_loaded_local_data = true;
		$class = static::model_type($this->type);
		if (!($local = $class::find($this->id))) return;
		foreach ($local->values() as $k => $v)
			$this->$k = $v;
	}
	
	public function load_content_data()
	{
		if ($this->__has_loaded_content_data) return;
		$this->__has_loaded_content_data = true;
		if (!($cd = Model_Content_Data::find($this->id))) return;
		foreach ($cd->values() as $k => $v)
			$this->$k = $v;
	}
	
	public function uuid()
	{
		return $this->uuid;
	}

	public function save()
	{
		if (!$this->__source && !$this->uuid)
			$this->uuid = UUID::create();
		parent::save();
	}
	
	public function is_scheduled()
	{
		if ($this->is_published) return false;
		if ($this->is_under_review) return false;
		if ($this->is_draft) return false;
		return true;
	}
	
	public function is_consume_locked()
	{
		if ($this->is_published) return true;
		if ($this->is_under_review) return true;
		if ($this->is_credit_locked) return true;
		if ($this->is_approved) return true;
		return false;
	}
	
	public function requires_credit()
	{
		if ($this->is_consume_locked())
			return false;
		
		$type = $this->type;
		if (in_array($this->type, 
				static::$__requires_credit))
			return true;
		return false;
	}	
	
	public function status_reset()
	{
		$this->is_credit_locked = 0;
		$this->is_approved = 0;
		$this->is_rejected = 0;
		$this->is_under_review = 0;
		$this->is_published = 0;
		$this->is_draft = 1;
		$this->save();
		
		// remove any approval or rejection data
		Model_Approval_Data::__delete($this->id);
		Model_Rejection_Data::__delete($this->id);

		if (($bundle = Model_Content_Distribution_Bundle::find($this->id))) 
		{
			$bundle->disable();
			$bundle->delete();
		}

		if (($m_content_data = Model_Content_Data::find($this->id)))
		{
			$m_content_data->is_social_twitter_locked = 0;
			$m_content_data->is_social_facebook_locked = 0;
			$m_content_data->save();
		}
	}
	
	public function requires_approval()
	{
		if ($this->is_approved) return false;
		if (in_array($this->type, static::$__requires_approval))
			return true;
		return false;
	}
	
	public function level()
	{
		if ($this->is_premium)
			return static::PREMIUM;
		return static::BASIC;
	}
	
	public function owner()
	{
		if (!$this->m_user)
		{
			$newsroom = $this->newsroom();
			if (!$newsroom) return false;
			$user = Model_User::find($newsroom->user_id);
			if (!$user) return false;
			$this->m_user = $user;
		}

		return $this->m_user;
	}

	public function newsroom()
	{
		if (!$this->m_newsroom)
		{
			$newsroom = Model_Newsroom::find($this->company_id);
			if (!$newsroom) return false;
			$this->m_newsroom = $newsroom;
		}

		return $this->m_newsroom;		
	}
	
	public function url_handler_social()
	{
		// this is slow! better to load
		// the source_url in the original SQL
		// where this is at all possible
		if (empty($this->media_type) ||
			 empty($this->post_id))
		     $social = Model_PB_Social::find($this->id);
		else $social = Model_PB_Social::from_object($this);
		return $social->url();
	}

	public function url($use_type = true, $slug = null)
	{
		// no slug provided? 
		if ($slug === null)
			$slug = $this->slug;

		// <URL_T>/<PR_ID> (legacy)
		if ($this->is_legacy)
			return $slug;

		// use a custom url generation handler
		$handler = "url_handler_{$this->type}";
		if (method_exists($this, $handler))
			return $this->$handler($slug);

		// view/<SLUG>
		if (!$use_type)
			return "view/{$slug}";
		
		// <CONTENT_TYPE>/<SLUG>
		$stype = static::slug_type($this->type, $this->id);
		return "{$stype}/{$slug}";
	}

	public function internal_url()
	{
		// internal url used to serve this content
		// at a url that isn't "correct" such 
		// as is the case with legacy 
		return "view/internal/{$this->id}";
	}
	
	public function permalink()
	{
		$ci =& get_instance();
		return $ci->website_url("view/id/{$this->id}");
	}
	
	public function url_id()
	{
		return $this->permalink();
	}
	
	public function url_raw()
	{
		$ci =& get_instance();
		return $ci->website_url("view/raw/{$this->id}");
	}
	
	public static function permalink_from_id($id)
	{
		$ci =& get_instance();
		return $ci->website_url("view/id/{$id}");
	}
	
	public static function allowed_types()
	{
		return array(
			static::TYPE_AUDIO,
			static::TYPE_BLOG,
			static::TYPE_EVENT,
			static::TYPE_IMAGE, 
			static::TYPE_NEWS, 
			static::TYPE_PR,
			static::TYPE_SOCIAL,
			static::TYPE_VIDEO,
		);
	}
	
	public static function is_allowed_type($type)
	{
		return in_array($type, static::allowed_types());
	}

	public static function internal_types()
	{
		return array(
			static::TYPE_AUDIO,
			static::TYPE_EVENT,
			static::TYPE_IMAGE, 
			static::TYPE_NEWS, 
			static::TYPE_PR,
			static::TYPE_VIDEO,
		);
	}

	public static function is_internal_type($type)
	{
		return in_array($type, static::internal_types());
	}
	
	public static function slug_type($type, $id)
	{
		// Confirm to Google News URI format
		if ($id && $id >= static::GNEWS_MIN_ID
			 && $type === static::TYPE_PR)
			return 'news';

		$display = array(
			static::TYPE_PR => 'press-release',
			static::TYPE_NEWS => 'news', 
			static::TYPE_IMAGE => 'image', 
			static::TYPE_AUDIO => 'audio',
			static::TYPE_VIDEO => 'video', 
			static::TYPE_EVENT => 'event',
			static::TYPE_BLOG => 'post', // BECAUSE 'blog' is being used by newswire blog
			static::TYPE_SOCIAL => 'social',
		);
		
		return @$display[$type];
	}

	public static function model_type($type)
	{
		$models = array(
			static::TYPE_PR => 'Model_PB_PR',
			static::TYPE_NEWS => 'Model_PB_News',
			static::TYPE_IMAGE => 'Model_PB_Image',
			static::TYPE_AUDIO => 'Model_PB_Audio',
			static::TYPE_VIDEO => 'Model_PB_Video',
			static::TYPE_EVENT => 'Model_PB_Event',
			static::TYPE_BLOG => 'Model_PB_Blog',
			static::TYPE_SOCIAL => 'Model_PB_Social',
		);
		
		return @$models[$type];
	}
	
	public static function full_type($type)
	{
		$display = array(
			static::TYPE_PR => 'Press Release',
			static::TYPE_NEWS => 'News', 
			static::TYPE_IMAGE => 'Image', 
			static::TYPE_AUDIO => 'Audio',
			static::TYPE_VIDEO => 'Video', 
			static::TYPE_EVENT => 'Event',
			static::TYPE_BLOG => 'Blog Post',
			static::TYPE_SOCIAL => 'Social',
		);
		
		return @$display[$type];
	}
	
	public static function full_type_plural($type)
	{
		$display = array(
			static::TYPE_PR => 'Press Releases',
			static::TYPE_NEWS => 'News', 
			static::TYPE_IMAGE => 'Images', 
			static::TYPE_AUDIO => 'Audio',
			static::TYPE_VIDEO => 'Videos', 
			static::TYPE_EVENT => 'Events',
			static::TYPE_BLOG => 'Blog Posts',
			static::TYPE_SOCIAL => 'Social Wire',
		);
		
		return @$display[$type];
	}
	
	public static function short_type($type)
	{
		$display = array(
			static::TYPE_PR => 'PR',
			static::TYPE_NEWS => 'News', 
			static::TYPE_IMAGE => 'Image', 
			static::TYPE_AUDIO => 'Audio',
			static::TYPE_VIDEO => 'Video', 
			static::TYPE_EVENT => 'Event',
			static::TYPE_BLOG => 'Blog',
			static::TYPE_SOCIAL => 'Social',
		);
		
		return @$display[$type];
	}
	
	public function approve()
	{	
		$this->is_approved = 1;
		$this->is_rejected = 0;
		$this->is_under_review = 0;
		$this->save();
		
		$iella_event = new Scheduled_Iella_Event();
		$iella_event->data->content = $this;
		$iella_event->schedule('content_approved');
	}
	
	public function reject()
	{
		$this->is_approved = 0;
		$this->is_rejected = 1;
		$this->is_under_review = 0;
		$this->is_published = 0;
		$this->is_draft = 1;
		$this->save();
		
		$iella_event = new Scheduled_Iella_Event();
		$iella_event->data->content = $this;
		$iella_event->schedule('content_rejected');
	}
	
	public function delete()
	{
		$this->db->delete('nr_content', array('id' => $this->id));
		$this->db->delete('nr_content_data', array('content_id' => $this->id));
		$this->db->delete('nr_content_tag', array('content_id' => $this->id));
		$this->db->delete('nr_content_image', array('content_id' => $this->id));
		$this->db->delete('nr_beat_x_content', array('content_id' => $this->id));
		$this->db->delete('nr_content_release_plus', array('content_id' => $this->id));
		$this->db->delete('nr_content_distribution_bundle', array('content_id' => $this->id));
		$this->db->delete("nr_pb_{$this->type}", array('content_id' => $this->id));
	}
	
	public function title_to_slug()
	{
		$this->slug = static::generate_slug($this->title, (int) $this->id, $this->type);
	}

	public function is_advert_supported()
	{
		if ($this->is_premium) return false;
		if (!($owner = $this->owner())) return false;
		return $owner->is_free_user();
	}

	public function untitled()
	{
		$this->title = static::__untitled();
	}

	public function is_untitled()
	{
		return static::__is_untitled($this->title);
	}

	public static function __untitled()
	{
		$title = substr(md5(microtime()), 0, 12);
		$title = sprintf(static::DEFAULT_TITLE_FORMAT, $title);
		return $title;
	}

	public static function __is_untitled($title)
	{
		if (preg_match(static::DEFAULT_TITLE_PATTERN, $title))
		     return true;
		else return false;
	}
	
	public static function generate_slug($title, $existing_id = 0, 
		$content_type = null, $available_check = true)
	{
		$length = static::MAX_SLUG_LENGTH;
		$suffix = null;

		if ($existing_id && $content_type === Model_Content::TYPE_PR)
		{
			$suffix = sprintf('-%d', $existing_id);
			if (!str_ends_with($title, $suffix))
			     $length -= strlen($suffix);
			else $suffix = null;
		}

		$ci =& get_instance();
		$slug = Slugger::create($title, $length);
		$sql = "SELECT 1 FROM nr_content WHERE slug = ? AND id != ?";
		
		while ($available_check)
		{			
			$params = array($slug, (int) $existing_id);
			$result = $ci->db->query($sql, $params);
			if (!$result->num_rows()) break;
			$slug = Slugger::create_with_random($title, $length);
		}

		return concat($slug, $suffix);
	}
	
}

?>