<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class PRN_DocX_Controller extends Admin_Base {

	public function index($id)
	{
		$content = Model_Content::find($id);
		$content->load_content_data();
		$content->load_local_data();
		$this->vd->content = $content;

		$html = $this->load->view('admin/publish/prn_docx', null, true);
		$in_buffer = File_Util::buffer_file();
		$out_buffer = File_Util::buffer_file();
		file_put_contents($in_buffer, $html);

		$htmltodocx = 'application/binaries/htmltodocx/htmltodocx';
		shell_exec(sprintf('cat %s | %s > %s',
			$in_buffer, $htmltodocx, $out_buffer));

		$download_name = "release-{$content->id}.docx";
		$download_size = filesize($out_buffer);
		$download_mime = MIME::BIN;

		$this->force_download($download_name, 
			$download_mime, $download_size);

		readfile($out_buffer);
		unlink($out_buffer);
		unlink($in_buffer);
	}

}

?>