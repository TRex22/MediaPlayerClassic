<?php

require_once "pwd.php";

/*****************************************************************************
 * Generic mySQL database access management class.  This can be used for	 *
 * implementing database access in other classes requiring it.  Features	 *
 * include:																	 *
 *	- suppressing of error messages and error management					 *
 *	- methods to control showing of error messages							 *
 *	- methods to perform and manage database connections and queries		 *
 *																			 *
 * The goal behind this class was to have an easy to extend mySQL management *
 * class.  Hopefully, others will find it useful.							 *
 *																			 *
 * Note:	Although not tested on systems running PHP3, it should be		 *
 *			compatible. If you run into any trouble, e-mail me with exact	 *
 *			details of the problem.  This "class" is being provided as is	 *
 * 			without any written warranties whatsoever.						 *
 *																			 *
 * Methods:																	 *
 *	- string get_dbhost()													 *
 *	- string get_dblogin()													 *
 *	- string get_dbpass()													 *
 *	- string get_dbname()													 *
 *	- void set_dbhost(string $value)										 *
 *	- void set_dblogin(string $value)										 *
 *	- void set_dbpass(string $value)										 *
 *	- void set_dbname(string $value)										 *
 *	- constructor void DB(string $dblogin, string $dbpass, string $dbname)	 *
 *	- int connect()															 *
 *	- void disconnect()														 *
 *	- string return_error(string $message)									 *
 *	- void showErrors()														 *
 *	- boolean hasErrors()													 *
 *	- void resetErrors()													 *
 *	- int query($sql)														 *
 *	- array fetchRow()														 *
 *	- int fetchLastInsertId()												 *
 *	- int resultCount()														 *
 *	- boolean resultExist()													 *
 *	- void clear(int $result = 0)											 *
 *****************************************************************************
 * Author:			Amir Khawaja											 *
 * E-mail:			amir@gorebels.net										 *
 * Date Created:	May 15, 2001											 *
 * Last Modified:	June 05, 2001											 *
 * Version:			1.0.1													 *
 *****************************************************************************
 * Change Log:																 *
 *																			 *
 * Version 1.0.1 -- June 05, 2001											 *
 *	+ added new method "int fetchLastInsertId()"							 *
 *	+ minor bug fixes														 *
 *****************************************************************************/

class DB
{

	/**
	  * global variables
	  */
	var $dbhost = ISDB_HOST;			//default database host
	var $dblogin;						//database login name
	var $dbpass;						//database login password
	var $dbname;						//database name
	var $dblink;						//database link identifier
	var $queryid;						//database query identifier
	var $error = array();				//storage for error messages
	var $record = array();				//database query record identifier
	var $totalrecords;					//the total number of records received from a select statement
	var $last_insert_id;				//last incremented value of the primary key
	
	/**
	  * get and set type methods for retrieving properties.
	  */
	
	function get_dbhost()
    {
        return $this->dbhost;
    } //end function
	
	function get_dblogin()
    {
        return $this->dblogin;
    } //end function
	
	function get_dbpass()
    {
        return $this->dbpass;
    } //end function
	
	function get_dbname()
    {
        return $this->dbname;
    } //end function
	
	function set_dbhost($value)
	{
    	return $this->dbhost = $value;
	} //end function
	
	function set_dblogin($value)
    {
        return $this->dblogin = $value;
    } //end function
	
	function set_dbpass($value)
    {
        return $this->dbpass = $value;
    } //end function
	
	function set_dbname($value)
    {
        return $this->dbname = $value;
    } //end function

	/**
	  * End of the Get and Set methods
	  */
	
	/**
      * Constructor
      *
      * @param      String $dblogin, String $dbpass, String $dbname
      * @return     void
      * @access     public
      */
	function DB($dblogin, $dbpass, $dbname)
    {
    	// REMOVEME
		if(isset($_ENV['COMPUTERNAME']) && $_ENV['COMPUTERNAME'] == 'AMD3500')
    		$this->dbhost = 'localhost';

    	$this->set_dblogin($dblogin);
		$this->set_dbpass($dbpass);
		$this->set_dbname($dbname);
    } //end function
	
	/**
      * Connect to the database and change to the appropriate database.
      *
      * @param      none
      * @return     database link identifier
      * @access     public
      * @scope      public
      */
	function connect()
    {
        $this->dblink = @mysql_pconnect($this->dbhost, $this->dblogin, $this->dbpass, MYSQL_CLIENT_COMPRESS);
		if(!$this->dblink)
		{
			$this->return_error("Unable to connect to the database.");
		}
		$t = @mysql_select_db($this->dbname, $this->dblink);
		if(!$t)
		{
			$this->return_error("Unable to change databases.");
		}
		
		if($this->dblink)
			mysql_query("SET NAMES 'utf8'", $this->dblink);
		
		return $this->dblink;
		
    } //end function
	
	/**
      * Disconnect from the mySQL database.
      *
      * @param      none
      * @return     void
      * @access     public
      * @scope      public
      */
	function disconnect()
    {
        if($this->dblink)
		{	//check to see that a connection exists.
			$test = @mysql_close($this->dblink);
			if(!$test)
			{
				$this->return_error("Unable to close the connection.");
			}
		}
		else
		{
		    $this->return_error("No connection open.");
		}
		unset($this->dblink);
    } //end function
	
	/**
      * Stores error messages
      *
      * @param      String $message
      * @return     String
      * @access     private
      * @scope      public
      */
	function return_error($message)
	{
		return $this->error[] = $message." ".mysql_error().".";
	} //end function
	
	/**
      * Show any errors that occurred.
      *
      * @param      none
      * @return     void
      * @access     public
      * @scope      public
      */
	function showErrors()
    {
        if($this->hasErrors())
		{
			reset($this->error);
			$errcount = count($this->error);	//count the number of error messages
			echo "<p>Error(s) found: <b>'$errcount'</b></p>\n";
			
			//print all the error messages.
			while(list($key, $val) = each($this->error))
			{
				echo "<li>$val</li><br>\n";
			}
			$this->resetErrors();
		}
    } //end function
	
	/**
      * Checks to see if there are any error messages that have been reported.
      *
      * @param      none
      * @return     boolean
      * @access     private
      */
	function hasErrors()
    {
        if(count($this->error) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
    } //end function
	
	/**
      * Clears all the error messages.
      *
      * @param      none
      * @return     void
      * @access     public
      */
	function resetErrors()
    {
        if($this->hasErrors())
		{
			unset($this->error);
			$this->error = array();
		}
    } //end function
	
	/**
      * Performs an SQL query.
      *
      * @param      String $sql
      * @return     int query identifier
      * @access     public
      * @scope      public
      */
	function query($sql)
    {	
		if(empty($this->dblink))
		{	//check to see if there is an open connection. If not, create one.
			$this->connect();
		}
		$t = @mysql_select_db($this->dbname, $this->dblink);
		if(!$t)
		{
			$this->return_error("Unable to change databases.");
		}
        $this->queryid = @mysql_query($sql, $this->dblink);
		if(!$this->queryid)
		{
			$this->return_error("Unable to perform the query <b>'$sql'</b>.");
		}
		return $this->queryid;
    } //end function

	/**
      * Grabs the records as a array.
      *
      * @param      none
      * @return     array of db records
      * @access     public
      */
	function fetchRow()
    {
		if(isset($this->queryid))
		{
        	return $this->record = @mysql_fetch_array($this->queryid);
		}
		else
		{
			$this->return_error("No query specified.");
		}
    } //end function

	/**
	  * If the last query performed was an "INSERT" statement, this method will
	  * return the last inserted primary key number. This is specific to the
	  * MySQL database server.
	  *
	  * @param		none
	  * @return		int
	  * @access		public
	  * @scope		public
	  * @since		version 1.0.1
	  */
	function fetchLastInsertId()
	{
		$this->last_insert_id = @mysql_insert_id($this->dblink);
		if(!$this->last_insert_id)
		{
			$this->return_error("Unable to get the last inserted id from MySQL.");
		}
		return $this->last_insert_id;
	} //end function

	/**
      * Counts the number of rows returned from a SELECT statement.
      *
      * @param      none
      * @return     Int
      * @access     public
      */
	function resultCount()
    {
        $this->totalrecords = @mysql_num_rows($this->queryid);
		if(!$this->totalrecords)
		{
			$this->return_error("Unable to count the number of rows returned");
		}
		return $this->totalrecords;
    } //end function
    
	function affectedRows()
    {
        $rows = @mysql_affected_rows($this->dblink);
        if($rows < 0)
        {
        	$this->return_error("Unable to get the number of affected rows");
        	$rows = 0;
        }
        return $rows;
    } //end function
	
	/**
      * Checks to see if there are any records that were returned from a
	  * SELECT statement. If so, returns true, otherwise false.
      *
      * @param      none
      * @return     boolean
      * @access     public
      */
	function resultExist()
    {
		if(isset($this->queryid) && ($this->resultCount() > 0))
		{
			return true;
		}
		return false;
    } //end function
	
	/**
      * Clears any records in memory associated with a result set.
      *
      * @param      Int $result
      * @return     void
      * @access     public
      */
	function clear($result = 0)
    {
		if($result != 0)
		{
			$t = @mysql_free_result($result);
			if(!$t)
			{
				$this->return_error("Unable to free the results from memory");
			}
		}
		else
		{
			if(isset($this->queryid))
			{
				$t = @mysql_free_result($this->queryid);
				if(!$t)
				{
					$this->return_error("Unable to free the results from memory (internal).");
				}
			}
			else
			{
			    $this->return_error("No SELECT query performed, so nothing to clear.");
			}
		}
	} //end function
	
	function enumsetValues($table, $column, $bitmaskkeys = false)
	{
		$values = array();
		
		$this->query("show columns from ".$table." like '".$column."'");
		if($row = $this->fetchRow())
		{
/*			$values = $row["Type"];
			$values = substr($values, 6, strlen($values)-8); 
			$values = str_replace("','",",",$values);
			$values = explode(",",$values);
*/			
			$values = explode("','",preg_replace("/(enum|set)\('(.+?)'\)/","\\2",$row[1]));
			
			if($bitmaskkeys)
			{
				$tmp = array();
				foreach($values as $i => $val) $tmp[1<<$i] = $val;
				$values = &$tmp;
			}
		}
		
		return $values;
	}
	
	function count($table, $field = '*')
	{
		$req = "select count($field) from $table";
		if(!$this->query($req) || !($row = $this->fetchRow()))
			return false;
		return $row[0];				
	}
	
	function fetchAll($req, &$rows)
	{
		$this->query($req);
		$rows = array();
		while($row = $this->fetchRow())
			$rows[] = $row;
	}
	
	function begin() {return $this->query("BEGIN");}
	function commit() {return $this->query("COMMIT");}
	function rollback() {return $this->query("ROLLBACK");}
	
}    //end class

define('ISDB_VERSION', 1);
define('ONEYEAR', 60*60*24*365);

class SubtitlesDB extends DB 
{
	function Create()
	{
		if(!ereg('localhost', $this->dbhost))
			return;
		
        ($dblink = @mysql_connect($this->dbhost, $this->dblogin."_init", $this->dbpass, MYSQL_CLIENT_COMPRESS)) or die(mysql_error());

        @mysql_query(
			"CREATE DATABASE IF NOT EXISTS `subtitles`",
			$dblink)
		or die(mysql_error());

		@mysql_query(
			"USE `subtitles`",
			$dblink)
		or die(mysql_error());

		@mysql_query(
			"CREATE TABLE IF NOT EXISTS `comments` ( ".
			"  `id` bigint(20) NOT NULL auto_increment, ".
			"  `subtitle_id` bigint(20) NOT NULL default '0', ".
			"  `nick` varchar(32) NOT NULL default '', ".
			"  `at` datetime NOT NULL default '0000-00-00 00:00:00', ".
			"  `content` mediumtext NOT NULL, ".
			"  `rating` tinyint(4) NOT NULL default '5', ".
			"  PRIMARY KEY  (`id`), ".
			"  KEY `subtitle_id` (`subtitle_id`), ".
			"  KEY `nick` (`nick`) ".
			") ENGINE=InnoDB DEFAULT CHARSET=utf8 ".
			"",
			$dblink)
		or die(mysql_error());

		@mysql_query(
			"CREATE TABLE IF NOT EXISTS `file` ( ".
			"  `id` bigint(20) NOT NULL auto_increment, ".
			"  `hash` varchar(16) NOT NULL default '', ".
			"  `size` varchar(16) NOT NULL default '', ".
			"  PRIMARY KEY  (`id`), ".
			"  KEY `hash` (`hash`), ".
			"  KEY `size` (`size`) ".
			") ENGINE=InnoDB DEFAULT CHARSET=utf8	 ".
			"",
			$dblink)
		or die(mysql_error());

		@mysql_query(
			"CREATE TABLE IF NOT EXISTS `file_subtitle` ( ".
			"  `id` bigint(20) NOT NULL auto_increment, ".
			"  `file_id` bigint(20) NOT NULL default '0', ".
			"  `subtitle_id` bigint(20) NOT NULL default '0', ".
			"  PRIMARY KEY  (`id`), ".
			"  KEY `file_id` (`file_id`), ".
			"  KEY `subtitle_id` (`subtitle_id`) ".
			") ENGINE=InnoDB DEFAULT CHARSET=utf8 ".
			"",
			$dblink)
		or die(mysql_error());

		@mysql_query(
			"CREATE TABLE IF NOT EXISTS `mirror` ( ".
			"  `id` bigint(20) NOT NULL auto_increment, ".
			"  `scheme` varchar(16) NOT NULL default '', ".
			"  `host` varchar(64) NOT NULL default '', ".
			"  `port` bigint(20) NOT NULL default '0', ".
			"  `path` varchar(64) NOT NULL default '', ".
			"  `name` varchar(64) NOT NULL default '', ".
			"  `lastseen` datetime NOT NULL default '0000-00-00 00:00:00', ".
			"  PRIMARY KEY  (`id`), ".
			"  KEY `lastseen` (`lastseen`) ".
			") ENGINE=InnoDB DEFAULT CHARSET=utf8 ".
			"",
			$dblink)
		or die(mysql_error());

		@mysql_query(
			"CREATE TABLE IF NOT EXISTS `movie` ( ".
			"  `id` bigint(20) NOT NULL auto_increment, ".
			"  `imdb` bigint(20) NOT NULL default '0', ".
			"  PRIMARY KEY  (`id`), ".
			"  UNIQUE KEY `imdb_2` (`imdb`), ".
			"  KEY `imdb` (`imdb`) ".
			") ENGINE=InnoDB DEFAULT CHARSET=utf8 ".
			"",
			$dblink)
		or die(mysql_error());

		@mysql_query(
			"CREATE TABLE IF NOT EXISTS `movie_subtitle` ( ".
			"  `id` bigint(20) NOT NULL auto_increment, ".
			"  `movie_id` bigint(20) NOT NULL default '0', ".
			"  `subtitle_id` bigint(20) NOT NULL default '0', ".
			"  `name` varchar(192) NOT NULL default '', ".
			"  `userid` bigint(20) NOT NULL default '0', ".
			"  `date` datetime NOT NULL default '0000-00-00 00:00:00', ".
			"  `notes` text NOT NULL, ".
			"  `format` enum('srt','sub','smi','ssa','ass','xss','other') NOT NULL default 'other', ".
			"  `iso639_2` varchar(3) NOT NULL default '', ".
			"  PRIMARY KEY  (`id`), ".
			"  KEY `movie_id` (`movie_id`), ".
			"  KEY `subtitle_id` (`subtitle_id`), ".
			"  KEY `format` (`format`), ".
			"  KEY `iso639_2` (`iso639_2`) ".
			" ) ENGINE=InnoDB DEFAULT CHARSET=utf8	 ".
			"",
			$dblink)
		or die(mysql_error());

		@mysql_query(
			"CREATE TABLE IF NOT EXISTS `subtitle` ( ".
			"  `id` bigint(20) NOT NULL auto_increment, ".
			"  `discs` tinyint(4) NOT NULL default '0', ".
			"  `disc_no` tinyint(4) NOT NULL default '0', ".
			"  `sub` mediumblob NOT NULL, ".
			"  `hash` varchar(32) NOT NULL default '', ".
			"  `mime` varchar(64) NOT NULL default '', ".
			"  `downloads` bigint(20) NOT NULL default '0', ".
			"  PRIMARY KEY  (`id`), ".
			"  UNIQUE KEY `hash` (`hash`), ".
			"  KEY `discs` (`discs`) ".
			") ENGINE=InnoDB DEFAULT CHARSET=utf8 ".
			"",
			$dblink)
		or die(mysql_error());

		@mysql_query(
			"CREATE TABLE IF NOT EXISTS `title` ( ".
			"  `id` bigint(20) NOT NULL auto_increment, ".
			"  `movie_id` bigint(20) NOT NULL default '0', ".
			"  `title` varchar(255) NOT NULL default '', ".
			"  PRIMARY KEY  (`id`), ".
			"  KEY `movie_id` (`movie_id`), ".
			"  KEY `title` (`title`) ".
			") ENGINE=InnoDB DEFAULT CHARSET=utf8 ".
			"",
			$dblink)
		or die(mysql_error());

		@mysql_query(
			"CREATE TABLE IF NOT EXISTS `user` ( ".
			"  `userid` bigint(20) NOT NULL auto_increment, ".
			"  `nick` varchar(16) NOT NULL default '', ".
			"  `passwordhash` varchar(32) NOT NULL default '', ".
			"  `email` varchar(64) NOT NULL default '', ".
			"  PRIMARY KEY  (`userid`), ".
			"  KEY `nick_pwh` (`nick`,`passwordhash`) ".
			") ENGINE=InnoDB DEFAULT CHARSET=utf8 ".
			"",
			$dblink)
		or die(mysql_error());

		@mysql_query(
			"CREATE TABLE IF NOT EXISTS `settings` ( ".
			"  `param` varchar(16) NOT NULL default '', ".
			"  `value` varchar(255) NOT NULL default '', ".
			"  PRIMARY KEY  (`param`) ".
			") ENGINE=InnoDB DEFAULT CHARSET=utf8 ".
			"",
			$dblink)
		or die(mysql_error());

		@mysql_query(
			"CREATE TABLE IF NOT EXISTS `accesslog` ( ".
			"  `http_user_agent` varchar(128) NOT NULL default '', ".
			"  `remote_addr` varchar(16) NOT NULL default '', ".
			"  `at` datetime NOT NULL default '0000-00-00 00:00:00', ".
			"  `php_self` varchar(255) NOT NULL default '', ".
			"  `userid` bigint(20) NOT NULL default '0' ".
			") ENGINE=InnoDB DEFAULT CHARSET=utf8 ".
			"",
			$dblink)
		or die(mysql_error());

		@mysql_close($dblink);
	}

	var $userid = 0;
	var $nick = '';
	var $passwordhash = '';
	var $email = '';
	
	function pwdhash($password)
	{
		return md5($password.'qwerty');
	}

	function authorizehash($nick, $passwordhash, $rememberme)
	{
		$this->query("select * from user where nick = '".addslashes($nick)."' && passwordhash = '$passwordhash'");
		if(!($row = $this->fetchRow())) return false;

		$this->userid = $row['userid'];
		$this->nick = $nick;
		$this->passwordhash = $passwordhash;
		$this->email = $row['email'];

		$_SESSION['user_nick'] = $nick;
		$_SESSION['user_passwordhash'] = $passwordhash;

		if($rememberme)
		{
			setcookie('user_nick', $nick, time() + ONEYEAR, '/');
			setcookie('user_passwordhash', $passwordhash, time() + ONEYEAR, '/');
		}
		
		return true;
	}

	function authorize($username, $password, $rememberme)
	{
		return $this->authorizehash($username, $this->pwdhash($password), $rememberme);
	}
	
	function getSetting($param)
	{
		$this->query("select value from settings where param = '$param'");
		if(!($row = $this->fetchRow())) return null;
		return $row[0];
	}
	
// public:	
	function SubtitlesDB()
	{
		$this->DB("gabest", GABESTS_PASSWORD_TO_SUBTITLES, "subtitles");
		
		$this->Create();
			
		$this->connect() or die('Cannot connect to database!');
		
		$version = intval($this->getSetting('version'));
		if($version != ISDB_VERSION) die('Wrong database client version, please upgrade this web interface!');
		
		// mirrors
		
		$http_host = split(':', $_SERVER['HTTP_HOST']);
		if(!isset($http_host[1])) $http_host[1] = 80;

		$db_scheme = addslashes(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on' ? 'https' : 'http');
		$db_host = addslashes($http_host[0]);
		$db_port = intval($http_host[1]);
		$db_path = addslashes(str_replace("\\", '/', dirname($_SERVER['PHP_SELF'])));
		if($db_path != '/') $db_path .= '/';
		global $ServerName;
		$db_name = addslashes(isset($ServerName) ? $ServerName : "");
		
		if(!empty($db_host)
		&& $db_host != 'localhost'
		&& $db_host != '127.0.0.1'
		&& !ereg('192\.168\.[0-9]+\.[0-9]+', $db_host)
		&& !ereg('10\.[0-9]+\.[0-9]+\.[0-9]+', $db_host))
		{
			$db_host_other = ereg('^www\.(.+)$', $db_host, $matches) ? $matches[1] : 'www.'.$db_host;
			
			$this->query("select id from mirror where host = '$db_host' || host = '$db_host_other'");
			if($row = $this->fetchRow())
			{
				$this->query(
					"update mirror set ".
					"scheme = '$db_scheme', host = '$db_host', port = $db_port, ".
					"path = '$db_path', name = '$db_name', lastseen = NOW() ".
					"where id = {$row['id']} ");
			}
			else
			{
				$this->query(
					"insert into mirror (scheme, host, port, path, name, lastseen) ".
					"values ('$db_scheme', '$db_host', $db_port, '$db_path', '$db_name', NOW()) ");
			}
		}
		
		// user

		if(isset($_SESSION['user_nick']) && isset($_SESSION['user_passwordhash'])
		&& $this->authorizehash($_SESSION['user_nick'], $_SESSION['user_passwordhash'], false))
		{
			$_SESSION['user_nick'] = $this->nick;
			$_SESSION['user_passwordhash'] = $this->passwordhash;
		}
		else if(isset($_COOKIE['user_nick']) && isset($_COOKIE['user_passwordhash'])
		&& $this->authorizehash($_COOKIE['user_nick'], $_COOKIE['user_passwordhash'], true))
		{
			$_SESSION['user_nick'] = $this->nick;
			$_SESSION['user_passwordhash'] = $this->passwordhash;
		}
		
		// accesslog
		
		$http_user_agent = addslashes($_SERVER['HTTP_USER_AGENT']);
		$remote_addr = addslashes($_SERVER['REMOTE_ADDR']);
		$remote_host = addslashes($_SERVER['REMOTE_HOST']);
		$php_self = addslashes($_SERVER['PHP_SELF']);
		
		$this->query(
			"insert into accesslog (http_user_agent, remote_addr, at, php_self, userid) ".
			"values ('$http_user_agent', '$remote_addr', NOW(), '$php_self', {$this->userid}) ");
	}
	
	function Login($username, $password, $rememberme)
	{
		$this->Logout();

		if(!$this->authorize($username, $password, $rememberme))
			return false;
		
		return true;
	}
	
	function Logout()
	{
		$this->userid = 0;
		$this->nick = '';
		$this->passwordhash = '';

		unset($_SESSION['user_nick']);
		unset($_SESSION['user_passwordhash']);

		setcookie('user_nick', '', time() - ONEYEAR, '/');
		setcookie('user_passwordhash', '', time() - ONEYEAR, '/');
	}
	
	function Register($nick, $password, $email)
	{
		$passwordhash = $this->pwdhash($password);
		$email = addslashes($email);

		return !!$this->query("insert into user (nick, passwordhash, email) values ('$nick', '$passwordhash', '$email')");
	}
	
	function IsLoggedIn()
	{
		return $this->userid > 0;
	}
}

function chkerr() {global $db; if($db->hasErrors()) {$db->showErrors(); exit;}}

$db = new SubtitlesDB();

global $smarty;
if(isset($smarty))
{
	unset($user);
	$user['userid'] = $db->userid;
	$user['nick'] = $db->nick;
	$smarty->assign('user', $user);
	unset($user);
}


?>