<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Text_Difference_Renderer {

	public function renderHTML($old, $new, $characters = 20)
	{
		$rendered = $this->renderDiff($old, $new);
		$differences = array();

		mb_regex_encoding('UTF-8');
		mb_regex_set_options('s');

		while (strlen($rendered) > 0)
		{
			mb_ereg_search_init($rendered);
			mb_ereg_search('<(ins|del)>');
			$matches = mb_ereg_search_getregs();
			if (!$matches) break;

			$openPosition = mb_strpos($rendered, $matches[0]);
			$closePosition = mb_strpos($rendered, sprintf('</%s>', $matches[1]));
			if ($closePosition === false) break;

			// change nearby?
			while (true)
			{
				$partial = mb_substr($rendered, $closePosition+6);
				if (!$partial) break;

				mb_ereg_search_init($partial);
				mb_ereg_search('<(ins|del)>');
				$matches = mb_ereg_search_getregs();
				if (!$matches) break;

				// next opening tag must be within 2x $characters
				$nextOpenPosition = mb_strpos($partial, $matches[0]);
				if ($nextOpenPosition > ($characters * 2)) break;
				$nextClosePosition = mb_strpos($partial, sprintf('</%s>', $matches[1]));
				if ($nextClosePosition === false) break;
				$closePosition = $nextClosePosition+$closePosition+6;
			}

			$startPosition = max(0, $openPosition-$characters);
			$endPosition = $closePosition+$characters+6;
			$innerLength = $closePosition+6-$openPosition;

			$charactersBefore = $openPosition-$startPosition;
			$charactersAfter = $endPosition-$closePosition;

			$differences[] = $this->correctHTML(implode(array(
				// remove any <ins> or <del> tags from the characters shown before the change
				preg_replace('#</?(ins|del)>#', null, mb_substr($rendered, $startPosition, $charactersBefore)),
				mb_substr($rendered, $startPosition+$charactersBefore, $innerLength),
				// remove any <ins> or <del> tags from the characters shown after the change
				preg_replace('#</?(ins|del)>#', null, mb_substr($rendered, $closePosition, $charactersAfter)),
			)));

			$rendered = mb_substr($rendered, $closePosition+6);
		}

		return implode('<omission></omission>', $differences);
	}

	protected function renderDiff($old, $new)
	{
		ob_start();

		$opCodes = FineDiff::getDiffOpcodes($old, $new, FineDiff::wordDelimiters);		
		$rendered = FineDiff::renderFromOpcodes($old, $opCodes, function($op, $from, $from_offset, $from_len) {

			if ($op === 'c')
			{
				echo substr($from, $from_offset, $from_len);	
			}
			else if ($op === 'd')
			{
				$deletion = substr($from, $from_offset, $from_len);
				echo sprintf('<del>%s</del>', $deletion);
			}
			else
			{
				$insertion = substr($from, $from_offset, $from_len);
				echo sprintf('<ins>%s</ins>', $insertion);
			}

		});

		$rendered = ob_get_contents();
		ob_end_clean();

		return $rendered;
	}

	protected function correctHTML($html)
	{
		// this function will attempt to open 
		// tags that are missing and close
		// tags that are not closed

		$format = '<root>
			<meta charset=utf-8 />
			<content>%s</content>
		</root>';

		$html = sprintf($format, $html);
		libxml_use_internal_errors(true);
		$dom = new DOMDocument();
		$opts = LIBXML_HTML_NOIMPLIED 
			   | LIBXML_HTML_NODEFDTD;
		$dom->loadHTML($html, $opts);

		$xpath = new DOMXPath($dom);
		foreach ($xpath->query('//*[not(node())]') as $node)
			$node->parentNode->removeChild($node);

		$html = null;
		foreach ($dom->childNodes[0]->childNodes[2]->childNodes as $child)
			$html .= $dom->saveHTML($child);

		return $html;
	}

}