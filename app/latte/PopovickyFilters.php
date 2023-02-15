<?php

namespace Andweb\Latte;

use Andweb,
Nette;

use Nette\Utils\Strings;


class PopovickyFilters
{
	static private $instance;

	static private $letters = [
		'a' => '[aáAÁ]',
		'b' => '[bB]',
		'c' => '[cčCČ]',
		'd' => '[dďDĎ]',
		'e' => '[eéEÉ]',
		'f' => '[fF]',
		'g' => '[gG]',
		'h' => '[hH]',
		'i' => '[iíIÍ]',
		'j' => '[jJ]',
		'k' => '[kK]',
		'l' => '[lL]',
		'm' => '[mM]',
		'n' => '[nN]',
		'o' => '[oO]',
		'p' => '[pP]',
		'q' => '[qQ]',
		'r' => '[rřRŘ]',
		's' => '[sšSŠ]',
		't' => '[tťTŤ]',
		'u' => '[uúůUÚŮ]',
		'v' => '[vV]',
		'w' => '[wW]',
		'x' => '[xW]',
		'y' => '[yýYÝ]',
		'z' => '[zžZŽ]',
	];

	public function __construct()
	{
		if (self::$instance !== null)
			throw new \Exception(__CLASS__ . ' is singleton and can be instanciead only once');

		self::$instance = $this;
	}

	public function register($template)
	{
		$template->addFilter(null, [self::class, 'common']);
	}

	public static function common($filter, $value)
	{
		if (method_exists(__CLASS__, $filter)) {
			$args = func_get_args();
			array_shift($args);
			return call_user_func_array([__CLASS__, $filter], $args);
		}
	}

	public static function relativeDate($date, $withTime = false)
	{
		if (!$date) {
			return '?';
		}

		$yesterday = new \DateTime('-1day');
		$today = new \DateTime('today');
		$tomorrow = new \DateTime('+1day');

		if ($date->format('Ymd') == $yesterday->format('Ymd')) {
			return ('VČERA') . ($withTime ? ' v  ' . $date->format('H:i') : '');
		}

		if ($date->format('Ymd') == $today->format('Ymd')) {
			return ('DNES') . ($withTime ? ' v  ' . $date->format('H:i') : '');
		}

		if ($date->format('Ymd') == $tomorrow->format('Ymd')) {
			return ('ZÍTRA') . ($withTime ? ' v  ' . $date->format('H:i') : '');
		}

		return ($date->format('j.n.Y')) . ($withTime ? ' v  ' . $date->format('H:i') : '');
	}


	public static function phone($s)
	{
		$s = preg_replace('~\D+~', '', $s);
		if (strpos($s, '+420') === false) {
			$s = '+420' . $s;
		}

		return $s;
	}


	public static function removeImages($s)
	{
		$s = preg_replace("/<img[^>]+\>/i", "", $s);

		return $s;
	}

	// public static function svg($s, $classes = null)
	// {
	// 	$svg =  file_get_contents($s);
	// 	$hasClass = strpos($svg, "class");

	// 	if ($hasClass && $classes !== null) {
	// 		$svg = str_replace('class="', 'class="' . $classes.' ', $svg);
	// 	} elseif (!$hasClass && $classes !== null) {
	// 		$svg = str_replace('<svg', '<svg class="' . $classes.'" ', $svg);
	// 	}

	// 	return $svg;
	// }


	public static function svg($s, $classes = null)
	{
		$svg = file_get_contents($s);
		$hasClass = strpos($svg, "class");

		if ($hasClass && $classes !== null) {
			$svg = str_replace('class="', 'class="' . $classes . ' ', $svg);
		} elseif (!$hasClass && $classes !== null) {
			$svg = str_replace('<svg', '<svg class="' . $classes . '" ', $svg);
		}

		return $svg;
	}

	public static function m2($s)
	{
		return str_replace('m2', 'm<sup>2</sup>', $s);
	}


	public static function mb_preg_match_all($ps_pattern, $ps_subject, &$pa_matches, $pn_flags = PREG_PATTERN_ORDER, $pn_offset = 0, $ps_encoding = NULL)
	{
		// WARNING! - All this function does is to correct offsets, nothing else:
		//
		if (is_null($ps_encoding))
			$ps_encoding = mb_internal_encoding();

		$pn_offset = strlen(mb_substr($ps_subject, 0, $pn_offset, $ps_encoding));
		$ret = preg_match_all($ps_pattern, $ps_subject, $pa_matches, $pn_flags, $pn_offset);

		if ($ret && ($pn_flags & PREG_OFFSET_CAPTURE))
			foreach ($pa_matches as &$ha_match)foreach ($ha_match as &$ha_match)
					$ha_match[1] = mb_strlen(substr($ps_subject, 0, $ha_match[1]), $ps_encoding);
		//
		// (code is independent of PREG_PATTER_ORDER / PREG_SET_ORDER)

		return $ret;
	}


	public static function cropAndSaveWords($string, $searchWord, $wordAround = 300)
	{
		$searchWord = explode(' ', $searchWord);
		$searchWord = array_filter($searchWord);
		$searchWord = array_map(function ($word) {
			$split = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY);
			$word = array_map(function ($letter) {
				$letter = Nette\Utils\Strings::lower($letter);
				$letter = Nette\Utils\Strings::toAscii($letter);
				if (isset(self::$letters[$letter]))
					$word = str_replace($letter, self::$letters[$letter], $letter);
				else
					$word = $letter;

				return $word;
			}
				, $split
			);

			return implode('', $word);

		}, $searchWord);


		$editedString = $string;
		$wordsJoin = implode('|', $searchWord);

		// \Tracy\Debugger::barDump($wordsJoin);
		// \Tracy\Debugger::barDump(strpos($editedString, 'Richter'), 'strpos');
		// \Tracy\Debugger::barDump(mb_strpos($editedString, 'Richter'), 'mb_strpos');

		// \Tracy\Debugger::barDump(Strings::matchAll($editedString, '~'.$wordsJoin.'~iu', PREG_OFFSET_CAPTURE), 'matchAll');

		$founded = [];
		$foundedPos = [];
		if (preg_match_all('~' . $wordsJoin . '~iu', $editedString, $matches, PREG_OFFSET_CAPTURE)) {
			foreach ($matches[0] as $match) {
				// \Tracy\Debugger::barDump($match, '$match');
				if (!isset($founded[$match[0]])) {
					//\Tracy\Debugger::barDump(Strings::length(substr($editedString, 0, $match[1])), 'kunda	');
					$founded[$match[0]] =
						mb_strlen(substr($editedString, 0, $match[1]));
					// POZOR !!! :)  delka znaků vs bytes
					// https://stackoverflow.com/questions/1725227/preg-match-and-utf-8-in-php

				}
			}

			$foundedPos = array_values($founded);
		}

		//return $string;

		$start = 0;
		$end = $wordAround;

		if (count($foundedPos)) {
			$start = max($foundedPos[0] - $wordAround, 0);
			$end = end($foundedPos);
		}

		// \Tracy\Debugger::barDump($founded, '$founded');
		//\Tracy\Debugger::barDump($foundedPos, '$foundePos');
		// \Tracy\Debugger::barDump($start, '$start');
		// \Tracy\Debugger::barDump($end, '$end');
		// \Tracy\Debugger::barDump($end - $start + $wordAround, '$end-$start');

		$finalResult = Nette\Utils\Strings::substring($editedString, $start, ($end - $start) + $wordAround) . ' ...';
		if ($start > 0) {
			$finalResult = explode(' ', $finalResult);
			unset($finalResult[0]);
			$finalResult = '... ' . implode(' ', $finalResult);
		}

		return $finalResult;

	}

	public static function zvyraznit($text, $q)
	{
		return $text;

	}


	public static function replaceOldMediaContent($s)
	{
		$s = str_replace('https://www.popovicky.cz/Frontend/Webroot/', '/storage/old/', $s);
		$s = str_replace('/Frontend/Webroot/', '/storage/old/', $s);

		return $s;
	}


	public static function markdown($s)
	{
		$Parsedown = new \Parsedown();
		$Parsedown->setSafeMode(true);
		$Parsedown->setMarkupEscaped(true);

		return $Parsedown->text($s);
	}

	public static function phoneCall($s)
	{
		$s = preg_replace('~\+(\d{3}) (\d{3}) (\d{3}) (\d{3})~', '<a href="tel:+$1$2$3$4">$0</a>', $s);

		return $s;
	}
}