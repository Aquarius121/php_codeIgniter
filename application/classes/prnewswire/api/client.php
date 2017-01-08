<?php

class PRNewswire_API_Client extends PRNewswire_API_Connection {

	const URI_ALLOWED_DISTRO       = 'Release/AllowedDistro';
	const URI_RELEASE_REPORT       = 'Report/%d';
	const URI_API_STATUS           = 'APIStatus';
	const URI_SUBMIT_RELEASE_NEW   = 'Release/New';
	const URI_UPLOAD_ASSET         = 'UploadAsset/File/%s';
	const URI_TERMS_AND_CONDITIONS = 'termsandconditions';

	const REQUEST_CONTENT_ID = 'RequestXML.xml';
	
	public function api_status()
	{
		$uri = static::URI_API_STATUS;
		$response = $this->request($uri);
		if (isset($response->data->Status))
		     return $response->data->Status;
		else return false;
	}

	public function terms_and_conditions()
	{
		$uri = static::URI_TERMS_AND_CONDITIONS;
		$response = $this->request($uri);
		if (isset($response->data->Method->TermsandConditions))
		     return $response->data->Method->TermsandConditions;
		else return false;
	}

	public function allowed_distro()
	{
		$uri = static::URI_ALLOWED_DISTRO;
		$response = $this->request($uri);
		if (isset($response->data->Method->Distributions))
		     return $response->data->Method->Distributions;
		else return false;
	}

	public function submit(PRNewswire_Release $release)
	{
		$this->submit_assets($release);

		$builder = new PRNewswire_API_Request_Builder();
		$dom = new DOMDocument('1.0', 'UTF-8');
		$_root = $dom->createElement('Request');
		$dom->appendChild($_root);

		$_method = $dom->createElement('Method');
		$_method->setAttribute('name',
			static::URI_SUBMIT_RELEASE_NEW);
		$_method->setAttribute('time', 
			Date::utc()->format(Date::FORMAT_ISO8601));
		$_root->appendChild($_method);

		$_param = $dom->createElement('Param');
		$_method->appendChild($_param);

		if (!$release->IsReleaseImmediate)
		{
			$_element = $dom->createElement('DistributionTimeDate');
			$_text = $dom->createTextNode($release->DistributionTimeDate->format(Date::FORMAT_ISO8601));
			$_element->appendChild($_text);
			$_param->appendChild($_element);
		}

		$_element = $dom->createElement('IsReleaseImmediate');
		$strbool = var_export((bool) $release->IsReleaseImmediate, true);
		$_text = $dom->createTextNode($strbool);
		$_element->appendChild($_text);
		$_param->appendChild($_element);

		$_element = $dom->createElement('Distributions');
		$_text = $dom->createTextNode(comma_separate($release->Distributions));
		$_element->appendChild($_text);
		$_param->appendChild($_element);

		$_element = $dom->createElement('Headline');
		$_text = $dom->createTextNode($release->Headline);
		$_element->appendChild($_text);
		$_param->appendChild($_element);

		if ($release->Subheadline)
		{
			$_element = $dom->createElement('Subheadline');
			$_text = $dom->createTextNode($release->Subheadline);
			$_element->appendChild($_text);
			$_param->appendChild($_element);
		}

		$_element = $dom->createElement('DatelineCity');
		$_text = $dom->createTextNode($release->DatelineCity);
		$_element->appendChild($_text);
		$_param->appendChild($_element);

		$_element = $dom->createElement('Source');
		$_text = $dom->createTextNode($release->Source);
		$_element->appendChild($_text);
		$_param->appendChild($_element);

		$_element = $dom->createElement('Body');
		$_text = $dom->createTextNode(base64_encode($release->Body));
		$_element->appendChild($_text);
		$_param->appendChild($_element);

		$_order_contact = $dom->createElement('OrderContact');
		$_param->appendChild($_order_contact);

			$_element = $dom->createElement('FirstName');
			$_text = $dom->createTextNode($this->config['user_first_name']);
			$_element->appendChild($_text);
			$_order_contact->appendChild($_element);

			$_element = $dom->createElement('LastName');
			$_text = $dom->createTextNode($this->config['user_last_name']);
			$_element->appendChild($_text);
			$_order_contact->appendChild($_element);

			$_element = $dom->createElement('Email');
			$_text = $dom->createTextNode($this->config['user_email']);
			$_element->appendChild($_text);
			$_order_contact->appendChild($_element);

			$_element = $dom->createElement('Phone');
			$_text = $dom->createTextNode($this->config['user_phone']);
			$_element->appendChild($_text);
			$_order_contact->appendChild($_element);

		// </OrderContact>

		$_element = $dom->createElement('UserEmailaddress');
		$_text = $dom->createTextNode($this->config['user_email']);
		$_element->appendChild($_text);
		$_param->appendChild($_element);

		$_element = $dom->createElement('ReportEmailaddress');
		$_text = $dom->createTextNode($release->ReportEmailaddress);
		$_element->appendChild($_text);
		$_param->appendChild($_element);

		foreach ($release->Images as $k => $Image)
		{
			$_image = $dom->createElement('Image');
			$_param->appendChild($_image);

			$_element = $dom->createElement('asset-ID');
			$_text = $dom->createTextNode($Image->AssetID);
			$_element->appendChild($_text);
			$_image->appendChild($_element);

			$_element = $dom->createElement('ImageCaption');
			$_text = $dom->createTextNode($Image->Caption);
			$_element->appendChild($_text);
			$_image->appendChild($_element);

			$_element = $dom->createElement('HiRes');
			$strbool = var_export((bool) $Image->HiRes, true);
			$_text = $dom->createTextNode($strbool);
			$_element->appendChild($_text);
			$_image->appendChild($_element);

			$_element = $dom->createElement('ImageArchival');
			$strbool = var_export((bool) $Image->ImageArchival, true);
			$_text = $dom->createTextNode($strbool);
			$_element->appendChild($_text);
			$_image->appendChild($_element);

			$_element = $dom->createElement('APPhotoExpress');
			$strbool = var_export((bool) $Image->APPhotoExpress, true);
			$_text = $dom->createTextNode($strbool);
			$_element->appendChild($_text);
			$_image->appendChild($_element);
		}

		if ($release->OnlineHostedPhoto)
		{
			$_image = $dom->createElement('OnlineHostedPhoto');
			$_param->appendChild($_image);

			$_element = $dom->createElement('asset-ID');
			$_text = $dom->createTextNode($release->OnlineHostedPhoto->AssetID);
			$_element->appendChild($_text);
			$_image->appendChild($_element);

			$_element = $dom->createElement('Caption');
			$_text = $dom->createTextNode($release->OnlineHostedPhoto->Caption);
			$_element->appendChild($_text);
			$_image->appendChild($_element);
		}

		if ($release->StandardPhoto)
		{
			$_image = $dom->createElement('StandardPhoto');
			$_param->appendChild($_image);

			$_element = $dom->createElement('asset-ID');
			$_text = $dom->createTextNode($release->StandardPhoto->AssetID);
			$_element->appendChild($_text);
			$_image->appendChild($_element);

			$_element = $dom->createElement('Caption');
			$_text = $dom->createTextNode($release->StandardPhoto->Caption);
			$_element->appendChild($_text);
			$_image->appendChild($_element);

			$_element = $dom->createElement('TSLVHeadline');
			$_text = $dom->createTextNode((new View_Data)->cut($release->StandardPhoto->Caption, 100));
			$_element->appendChild($_text);
			$_image->appendChild($_element);
		}

		foreach ($release->Youtube as $k => $Youtube)
		{
			$_youtube = $dom->createElement('youtube');
			$_param->appendChild($_youtube);

			$_element = $dom->createElement('YouTubevideoURL');
			$_text = $dom->createTextNode($Youtube->URL);
			$_element->appendChild($_text);
			$_youtube->appendChild($_element);

			$_element = $dom->createElement('YouTubeCaption');
			$_text = $dom->createTextNode($Youtube->Caption);
			$_element->appendChild($_text);
			$_youtube->appendChild($_element);
		}

		$_element = $dom->createElement('TCacknowledgement');
		$_text = $dom->createTextNode('true');
		$_element->appendChild($_text);
		$_param->appendChild($_element);

		$_element = $dom->createElement('SpecialInstructions');
		$_text = $dom->createTextNode($release->SpecialInstructions);
		$_element->appendChild($_text);
		$_param->appendChild($_element);

		$dom->formatOutput = true;
		$builder->add_string($dom->saveXML(), 
			static::REQUEST_CONTENT_ID, 'text/xml');

		$uri = static::URI_SUBMIT_RELEASE_NEW;
		$response = $this->request($uri, $builder->body());

		// looks like everything went well? return the id
		if (isset($response->data->Method->ReleaseReferenceNumber) && 
			 isset($response->data->Method->ResponseStatus) && 
			       $response->data->Method->ResponseStatus == 100)
			return (int) $response->data->Method->ReleaseReferenceNumber;

		// something went wrong, throw error (from API)
		if (isset($response->data->Method->CAPIError->Error))
		{
			$code = $response->data->Method->CAPIError->Error->errorCode;
			$string = $response->data->Method->CAPIError->Error->errorString;
			$message = sprintf('%s: %s', $code, $string);
			throw new PRNewswire_API_Exception($message);
		}

		// something went wrong, throw error
		throw new Exception(isset($response->body)
			? $response->body
			: var_export($this->_request, true));
	}

	protected function submit_assets($release)
	{
		if ($release->OnlineHostedPhoto)
			$this->submit_asset($release->OnlineHostedPhoto);
		if ($release->StandardPhoto)
			$this->submit_asset($release->StandardPhoto);
		foreach ($release->Images as $k => $Image)
			$this->submit_asset($Image);
	}

	protected function submit_asset($Image)
	{
		$filename = basename($Image->File);
		$uri = sprintf(static::URI_UPLOAD_ASSET, rawurlencode($filename));
		$data = base64_encode(file_get_contents($Image->File));
		$hash = md5($data);

		if (($mAsset = Model_PRN_Distribution_Asset::find($hash)))
		{
			$Image->AssetID = $mAsset->id;
			return;
		}

		$mime = File_Util::detect_mime($Image->File);
		$length = strlen($data);

		$buffer[] = sprintf('Content-Type: %s', $mime);
		$buffer[] = sprintf('Content-Transfer-Encoding: Base64');
		$buffer[] = sprintf('Content-ID: %s', $filename);
		$buffer[] = sprintf('Content-Length: %d', $length);
		$buffer[] = $data;

		$buffer = implode(PHP_EOL, $buffer);
		$response = $this->request($uri, $buffer);

		// looks like everything went well? return the id
		if (isset($response->data->Method->AssetID) && 
			 isset($response->data->Method->ResponseStatus) && 
			       $response->data->Method->ResponseStatus == 100)
		{
			$Image->AssetID = $response->data->Method->AssetID;
			$mAsset = new Model_PRN_Distribution_Asset();
			$mAsset->hash = $hash;
			$mAsset->id = $Image->AssetID;
			$mAsset->save();
			return;
		}

		// something went wrong, throw error (from API)
		if (isset($response->data->Method->CAPIError->Error))
		{
			$code = $response->data->Method->CAPIError->Error->errorCode;
			$string = $response->data->Method->CAPIError->Error->errorString;
			$message = sprintf('%s: %s', $code, $string);
			throw new PRNewswire_API_Exception($message);
		}

		// something went wrong, throw error
		throw new Exception($response->body);
	}

	public function report($releaseReferenceNumber)
	{
		$uri = static::URI_RELEASE_REPORT;
		$uri = sprintf($uri, $releaseReferenceNumber);
		$response = $this->request($uri);
		var_dump($response);
		// if (isset($response->data->Method->Distributions))
		//      return $response->data->Method->Distributions;
		// else return false;
	}
	
}