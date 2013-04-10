<?php
/**
 * Apache Common Log Parser
 *
 * This file parses the 'common' Apache log format to retrieve data in easy arrays.
 * It makes pulling data from large log files efficient when a specific date range is required.
 * Regex taken from a parser written by Sam Clarke (@link http://www.phpclasses.org/package/2596-PHP-Parse-Apache-log-files-in-the-common-log-format.html)
 *
 * @version 1.0
 * @package ApacheCommonLogParser
 * @author Aaron Pollock <aaron.pollock@gmail.com>
 * @copyright Copyright (c) 2011, Aaron Pollock
 * @license http://creativecommons.org/licenses/by/3.0/ Creative Commons Attribution 3.0 Unported
 */

/**
 * The main parser class, one instance per logfile.
 *
 * @package ApacheCommonLogParser
 */
class CommonLogParser
{

	/**
	 * The logfile PHP resource
	 *
	 * @access private
	 * @var resource
	 */
	private $_fp;

	/**
	 * Newline character
	 *
	 * @access private
	 * @var string
	 */
	private $_newline;

	/**
	 * Minimum seek line used for searching in (@link move_pointer_to_time())
	 *
	 * @access private
	 * @var int
	 */
	private $_min_seek_line;

	/**
	 * Maximum seek line used for searching in (@link move_pointer_to_time())
	 *
	 * @access private
	 * @var int
	 */
	private $_max_seek_line;

	/**
	 * Constructor sets up member vars
	 *
	 * @access public
	 * @param string $filepath Absolute path to the logfile being parsed (Apache "common" format)
	 * @param string $newline Optional newline character (defaults to "\n")
	 * @return bool Result of (@link _open_log_file())
	 */
	public function __construct($filepath, $newline="\n")
	{
		$this->_newline = $newline;
		return $this->_open_log_file($filepath);
	}

	/**
	 * Open log file for reading, sets up (@link $_fp)
	 *
	 * @access private
	 * @param string $filepath Absolute path to the logfile being parsed
	 * @return bool True on success, false on failure
	 */
	private function _open_log_file($filepath)
	{
		$this->_fp = fopen($filepath, 'r');
		if (false === $this->_fp) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Close log file resource
	 *
	 * @access private
	 * @return bool True on success, false on failure or if not open
	 */
	private function _close_log_file()
	{
		if (!is_resource($this->_fp)){
			return false;
		}
		return fclose($this->_fp);
	}

	/**
	 * Destructor, closes log file just in case not closed manually
	 *
	 * @access public
	 * @return void
	 */
	public function __destruct()
	{
		$this->_close_log_file();
	}

	/**
	 * Get next raw logfile line from pointer (@link $_fp)
	 *
	 * @return string|bool Raw logfile line, excluding newline at the end. Returns false if end of file.
	 * @access private
	 */
	private function _next_raw_line()
	{
		if (feof($this->_fp)) {
			return false;
		}

		$line = fgets($this->_fp);

	    return rtrim($line, $this->_newline);
	}

	/**
	 * Get raw logfile line by line number
	 *
	 * @param int $line_number The line number to retrieve
	 * @return string|bool Raw logfile line, excluding newline at the end. False if line does not exist.
	 * @access private
	 */
	public function _get_raw_line($line_number)
	{
		fseek($this->_fp, 0);

		for ($i=1; $i<=$line_number; $i++) {
			if (false === ($line = fgets($this->_fp))) {
				return false;
			}
		}

		return rtrim($line, $this->_newline);
	}

	/**
	 * Parse raw logfile line to get array of log values
	 *
	 * @access private
	 * @param string $raw_line
	 * @return array|bool Data from log entry or false if line format is wrong
	 */
	private function _parse_line($raw_line)
	{
		$result = preg_match("/^(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] \"(\S+) (.*?) (\S+)\" (\S+) (\S+) (\".*?\") (\".*?\")$/", $raw_line, $data);

		if ($result == 0 || !array_key_exists(0, $data)) {
			return false;
		}

		return array(
			'ip_address'	=> trim($data[1], '"'),
			'identity'		=> trim($data[2], '"'),
			'user'			=> trim($data[3], '"'),
			'unix_time'		=> strtotime(str_replace('/', ' ', $data[4]) . ' ' . $data[5] . ' ' . $data[6]),
			'method'		=> trim($data[7], '"'),
			'path'			=> trim($data[8], '"'),
			'protocol'		=> trim($data[9], '"'),
			'status'		=> trim($data[10], '"'),
			'bytes'			=> trim($data[11], '"'),
			'referrer'		=> trim($data[12], '"'),
			'user_agent'	=> trim($data[13], '"')
		);
	}

	/**
	 * Get next line from pointer
	 *
	 * @access public
	 * @return array|bool Data from line or false if end of file reached, or parsing failed
	 */
	public function get_next_line()
	{
		$line = $this->_next_raw_line();
		if ($line === false) {
			return false;
		}

		return $this->_parse_line($line);
	}

	/**
	 * Get line by number from parser
	 *
	 * @access public
	 * @param int $line_number Line number to retrieve
	 * @return array|bool Data from log entry or false if line not retrieved or malformed
	 */
	public function get_line($line_number)
	{
		$line = $this->_get_raw_line($line_number);
		if (false === $line) {
			return false;
		}

		return $this->_parse_line($line);
	}

	/**
	 * Count lines in file
	 *
	 * @access public
	 * @return int Number of lines in logfile
	 */
	public function count_lines()
	{
		fseek($this->_fp, 0);
		$i = 0;
		while (false !== fgets($this->_fp)){
			$i++;
		}
		return $i;
	}

	/**
	 * Move pointer to specific Unix time in logfile
	 *
	 * @access public
	 * @param int $time Seconds since epoch where the pointer should be positioned
	 * @return bool True on success. False if outside file bounds.
	 */
	public function move_pointer_to_time($time)
	{
		$count_lines = $this->count_lines();

		// assign start and end times from log file to $lines_times
		$start = $this->get_line(1);
		$end = $this->get_line($count_lines);
		$lines_times[1] = $start['unix_time'];
		$lines_times[$count_lines] = $end['unix_time'];

		// return false if time sought is outside bounds
		if ($time < $lines_times[1] || $time > $lines_times[$count_lines]){
			return false;
		}

		// setup minimum and maximum possible line numbers between which sought line lies
		$this->_min_seek_line = 1;
		$this->_max_seek_line = $count_lines;

		if ($this->_seek_to_time($time)) {

			// set pointer to just before $_min_seek_line by getting the line before
			if ($this->_min_seek_line == 1) {
				fseek($this->_fp, 0);
			} else {
				$this->get_line($this->_min_seek_line);
			}

			$this->_min_seek_line = null;
			$this->_max_seek_line = null;

			return true;

		} else {
			return false;
		}
	}

	/**
	 * Seek to time
	 *
	 * @access private
	 * @param int $time Unix time to which pointer should be moved
	 * @return bool True on success, false on failure
	 *
	 * This recursive function is wrapped by (@link move_pointer_to_time()) and should not be called directly
	 */
	private function _seek_to_time($time)
	{
		// get halfway point between $_min_seek_line and $_max_seek_line
		$halfway_line_number = floor( ($this->_max_seek_line - $this->_min_seek_line) / 2 ) + $this->_min_seek_line;
		$halfway_entry = $this->get_line($halfway_line_number);
		$halfway_time = $halfway_entry['unix_time'];

		// if $_max_seek_line - $_min_seek_line <= 1, return true (we're between two log entries or, less likely, right on the time)
		if ($this->_max_seek_line - $this->_min_seek_line <= 1) {
			return true;

		// else if $halfway-time is equal to $time, set $_min_seek_line to $halfway_line_number, and return true
		} elseif ($halfway_time == $time) {
			$this->_min_seek_line = $halfway_line_number;
			return true;

		// else if $halfway_time is greater than seek time, $halfway_line_number line becomes the new $_max_seek_line, and we recurse
		} elseif ($halfway_time > $time) {
			$this->_max_seek_line = $halfway_line_number;
			return $this->_seek_to_time($time);

		// else if $halfway_time is less than the seek time, $halfway_line_number line becomes the new $_min_seek_line, and we recurse
		} elseif ($halfway_time < $time) {
			$this->_min_seek_line = $halfway_line_number;
			return $this->_seek_to_time($time);

		} else {
			return false;
		}

	}


}
?>