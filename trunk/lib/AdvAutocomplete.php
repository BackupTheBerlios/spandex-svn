<?php
/**
 * Project Spandex
 * Copyright (c) 2006 Sydney PHP User Group
 *  http://www.sydphp.org/
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License
 * as published by the Free Software Foundation; either version 2.1
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * See licence.txt for more details
 **/
 
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/text.php';

/**
 * Class to dynamically create a filter for an HTML SELECT
 *
 * @author       Nicolas Hoizey <nicolas@hoizey.com>
 * @version      1.0
 * @since        PHP4.04pl1
 * @access       public
 */
class HTML_QuickForm_AdvAutocomplete extends HTML_QuickForm_text {
    
    // {{{ properties

    /**
     * Contains the possible values to use for autocomplete
     *
     * @var       array
     * @since     1.0
     * @access    private
     */
    var $_options = array();
    // }}}
    // {{{ constructor
        
    /**
     * Class constructor
     * 
     * @param     string    $elementName    (optional)Input field name attribute
     * @param     string    $elementLabel   (optional)Input field label
     * @param     mixed     $attributes     (optional)Either a typical HTML attribute string 
     *                                      or an associative array
     * @since     1.0
     * @access    public
     * @return    void
     */
    function HTML_QuickForm_AdvAutocomplete($elementName = null, $elementLabel = null, $attributes = null)
    {
        $this->HTML_QuickForm_text($elementName, $elementLabel, array_merge($attributes,array('autocomplete' => 'off')));
    } //end constructor
    
    // }}}
    // {{{ apiVersion()

    /**
     * Returns the current API version 
     * 
     * @since     1.0
     * @access    public
     * @return    double
     */
    function apiVersion()
    {
        return 1.0;
    } //end func apiVersion

    // }}}
    // {{{ addOption()

    /**
     * Adds a new item to use for auto complete
     *
     * @param     string    $text       Display text for the OPTION
     * @since     1.0
     * @access    public
     * @return    void
     */
    function addOption($text)
    {
	if (is_null($this->_options)) {
	    $this->_options = array($text);
	} elseif (!in_array($text, $this->_options)) {
	    $this->_options[] = $text;
	}
    } // end func addOption
    
    // }}}
    // {{{ loadArray()

    /**
     * Loads the options from an array
     * 
     * @param     array    $arr     Array of options
     * @since     1.0
     * @access    public
     * @return    PEAR_Error on error or true
     * @throws    PEAR_Error
     */
    function loadArray($arr)
    {
        if (!is_array($arr)) {
            return PEAR::raiseError('Argument 1 of HTML_Select::loadArray is not a valid array');
        }
        foreach ($arr as $val) {
            $this->addOption($val);
        }
        return true;
    } // end func loadArray

    // }}}
    // {{{ loadDbResult()

    /**
     * Loads the options from DB_result object
     * 
     * If no column names are specified the first two columns of the result are
     * used as the text and value columns respectively
     * @param     object    $result     DB_result object 
     * @param     string    $textCol    (optional) Name of column to display as the OPTION text 
     * @since     1.0
     * @access    public
     * @return    PEAR_Error on error or true
     * @throws    PEAR_Error
     */
    function loadDbResult(&$result, $textCol=null)
    {
        if (!is_object($result) || !is_a($result, 'db_result')) {
            return PEAR::raiseError('Argument 1 of HTML_Select::loadDbResult is not a valid DB_result');
        }
        $fetchMode = ($textCol) ? DB_FETCHMODE_ASSOC : DB_FETCHMODE_DEFAULT;
        while (is_array($row = $result->fetchRow($fetchMode)) ) {
            if ($fetchMode == DB_FETCHMODE_ASSOC) {
                $this->addOption($row[$textCol]);
            } else {
                $this->addOption($row[0]);
            }
        }
        return true;
    } // end func loadDbResult
    
    // }}}
    // {{{ loadQuery()

    /**
     * Queries a database and loads the options from the results
     *
     * @param     mixed     $conn       Either an existing DB connection or a valid dsn 
     * @param     string    $sql        SQL query string
     * @param     string    $textCol    (optional) Name of column to display as the OPTION text 
     * @since     1.1
     * @access    public
     * @return    void
     * @throws    PEAR_Error
     */
    function loadQuery(&$conn, $sql, $textCol=null)
    {
        if (is_string($conn)) {
            require_once('DB.php');
            $dbConn = &DB::connect($conn, true);
            if (DB::isError($dbConn)) {
                return $dbConn;
            }
        } elseif (is_subclass_of($conn, "db_common")) {
            $dbConn = &$conn;
        } else {
            return PEAR::raiseError('Argument 1 of HTML_Select::loadQuery is not a valid type');
        }
        $result = $dbConn->query($sql);
        if (DB::isError($result)) {
            return $result;
        }
        $this->loadDbResult($result, $textCol);
        $result->free();
        if (is_string($conn)) {
            $dbConn->disconnect();
        }
        return true;
    } // end func loadQuery

    // }}}
    // {{{ load()

    /**
     * Loads options from different types of data sources
     *
     * This method is a simulated overloaded method.  The arguments, other than the
     * first are optional and only mean something depending on the type of the first argument.
     * If the first argument is an array then all arguments are passed in order to loadArray.
     * If the first argument is a db_result then all arguments are passed in order to loadDbResult.
     * If the first argument is a string or a DB connection then all arguments are 
     * passed in order to loadQuery.
     * @param     mixed     $options     Options source currently supports assoc array or DB_result
     * @param     mixed     $param1     (optional) See function detail
     * @param     mixed     $param2     (optional) See function detail
     * @since     1.1
     * @access    public
     * @return    PEAR_Error on error or true
     * @throws    PEAR_Error
     */
    function load(&$options, $param1=null, $param2=null)
    {
        switch (true) {
            case is_array($options):
                return $this->loadArray($options);
                break;
            case (is_a($options, 'db_result')):
                return $this->loadDbResult($options, $param1);
                break;
            case (is_string($options) && !empty($options) || is_subclass_of($options, "db_common")):
                return $this->loadQuery($options, $param1, $param2);
                break;
        }
    } // end func load
    
    // }}}
    // {{{ toHtml()

    /**
     * Returns the filter in HTML
     * 
     * @since     1.0
     * @access    public
     * @return    string
     */
    function toHtml()
    {
	$arrayName = str_replace(array('[', ']'), array('__', ''), $this->getName()) . '_values';
	$onFocus = 'javascript:QF_AdvAutocomplete_Focus(this, '. $arrayName .');';

        $this->updateAttributes(array('onfocus' => $onFocus));

        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } else {
            $tabs = $this->_getTabs();
            $js = '<script type="text/javascript">';
	    $js .= "\n";
	    $js .= '//<![CDATA[';
	    $js .= "\n";
	    if (!defined('HTML_QUICKFORM_ADVAUTOCOMPLETE_EXISTS')) {
                $js  .= <<< EOS
// begin javascript for filtered select
qf_AdvAC.configure = function()
{
  // Timeout (in ms) after which the list is automatically hidden
  qf_AdvAC.config.hideTimeout     = 15000;
  // Timeout (in ms) after which the search is began
  qf_AdvAC.config.acTimeout       = 250;
  // How many items have to stay visibile in the list
  qf_AdvAC.config.listMaxItem     = 10;
  // How many characters are required before auto complete
  qf_AdvAC.config.minchar = 0;
}

qf_AdvAC.onInputKeyUp = function ( hEvent )
{
  if (qf_AdvAC.hideTimer != null) 
    clearTimeout(qf_AdvAC.hideTimer);
    
  qf_AdvAC.hideTimer = setTimeout("qf_AdvAC.hideList()", qf_AdvAC.config.hideTimeout);
  
  if (qf_AdvAC.input.value.length < qf_AdvAC.config.minchar && qf_AdvAC.listShown)
    qf_AdvAC.hideList();      
}

qf_AdvAC.onInputKeyDown = function ( hEvent )
{
  if(!hEvent) 
    hEvent = window.event;
    
  switch(hEvent.keyCode)
  {
    case 37: // left arrow
    case 39: // right arrow
    case 33: // page up  
    case 34: // page down  
    case 36: // home  
    case 35: // end
    case 27: // esc
    case 16: // shift  
    case 17: // ctrl  
    case 18: // alt  
    case 20: // caps lock
    case 38: // up arrow
    case 40: // down arrow
      break;
    default:
      break;
  }
    
  switch(hEvent.keyCode)
  {
    case 13:
    case 39: // Enter & Right
      qf_AdvAC.setValue();
      qf_AdvAC.clearList();
      qf_AdvAC.hideList();
      hEvent.returnValue = false;
      break;
      
    case 40: // Down 
      if (!qf_AdvAC.listShown)
      {
        if (qf_AdvAC.acTimer != null) 
          clearTimeout(qf_AdvAC.acTimer);
        qf_AdvAC.autoComplete();    
        qf_AdvAC.selectCurrent();
        break;
      }

      if (qf_AdvAC.container && qf_AdvAC.list.hasChildNodes())
      {
        if (qf_AdvAC.activeItem == null)
        {
          try {
            qf_AdvAC.activeItem = qf_AdvAC.list.childNodes.item(0).firstChild;
            qf_AdvAC.activeItemIdx = 0;
            qf_AdvAC.windowMax = qf_AdvAC.config.listMaxItem - 1;
            qf_AdvAC.windowMin = 0;
          }
          catch(e) {}
        }
        else
        {
          try {
            nextItem = qf_AdvAC.list.childNodes.item(qf_AdvAC.activeItemIdx + 1).firstChild;
            qf_AdvAC.unselectCurrent();
            qf_AdvAC.activeItem = nextItem;
            qf_AdvAC.activeItemIdx++;
          } 
          catch(e) {}
        }
      }
      qf_AdvAC.selectCurrent();
      break;

    case 37: // Left
      break;
              
    case 38: // Up
      if (!qf_AdvAC.listShown)
        break;
        
      if (qf_AdvAC.container && qf_AdvAC.list.hasChildNodes())
      {
        if (qf_AdvAC.activeItem != null)
        {
          qf_AdvAC.unselectCurrent();
          if (qf_AdvAC.activeItemIdx > 0)
          {
            qf_AdvAC.activeItemIdx--;
            qf_AdvAC.activeItem = qf_AdvAC.list.childNodes.item(qf_AdvAC.activeItemIdx).firstChild;
          }
        }
      }
      qf_AdvAC.selectCurrent();
      break;

    case 27: // Esc
      if (!qf_AdvAC.listShown)
        break;
      qf_AdvAC.clearList();
      qf_AdvAC.hideList();
      break;
      
    case 9: // TAB
      qf_AdvAC.hideList();
      qf_AdvAC.onInputBlur();
      break;
                        
    default:  
      if (qf_AdvAC.acTimer != null) 
        clearTimeout(qf_AdvAC.acTimer);
      qf_AdvAC.acTimer = setTimeout("qf_AdvAC.autoComplete()", qf_AdvAC.config.acTimeout);    
      break;
  }
  
  return hEvent.returnValue;
}

qf_AdvAC.setValue = function ()
{
  if (qf_AdvAC.activeItem)
  {
    qf_AdvAC.input.value = qf_AdvAC.activeItem.innerHTML.replace(/<strong>(.*)<\/strong>/ig, "$1");
  }
}

qf_AdvAC.getMatchingElementKey = function(idx)
{
  var i=0;
  for (match in qf_AdvAC.matches)
  {
    if (i == idx)
      return match;
    i++;
  }
}

qf_AdvAC.onFormSubmit = function (hEvent)
{
  if( hEvent == null )
    hEvent = window.event;

      qf_AdvAC.clearList();
      qf_AdvAC.hideList();
      if (qf_AdvAC.acTimer != null) 
        clearTimeout(qf_AdvAC.acTimer);
}

qf_AdvAC.onInputBlur = function ( hEvent )
{
  if (qf_AdvAC.listShown)
    return;
    
  delete qf_AdvAC.activeItem;
  qf_AdvAC.activeItemIdx = 0;
  if (qf_AdvAC.container)
  {
    qf_AdvAC.clearList();
    qf_AdvAC.container.parentNode.removeChild(qf_AdvAC.container);
    qf_AdvAC.container = null;
  }
}

qf_AdvAC.autoComplete = function ()
{
  var pattern = qf_AdvAC.input.value.toLowerCase().trim();
  if (pattern.length < qf_AdvAC.config.minchar)
    return;
    
  // Look through array given as plugin
    qf_AdvAC.matches = qf_AdvAC.plugin;
  qf_AdvAC.feedList();
}


qf_AdvAC.feedList = function()
{
  qf_AdvAC.clearList();
        
  qf_AdvAC.activeItem = null;
  qf_AdvAC.activeItemIdx = 0;
  
  var pattern = qf_AdvAC.input.value.toLowerCase().trim();
  hItem = null;
  for (match in qf_AdvAC.matches)
  {
    idx = qf_AdvAC.matches[match].toLowerCase().indexOf(pattern);

    if (idx != -1 || qf_AdvAC.matches[match] == "???")
    {
      hItem = document.createElement("li");
      hAnchor = document.createElement("a");
      hAnchor.href="#";
      hAnchor.innerHTML  = qf_AdvAC.matches[match].substring(0, idx) + "<strong >" + qf_AdvAC.matches[match].substring(idx, idx + pattern.length) + "</strong >"  + qf_AdvAC.matches[match].substring(idx + pattern.length);
      
      if (hAnchor.attachEvent) 
      {
        hAnchor.attachEvent('onclick',     qf_AdvAC.onItemClick);
        hAnchor.attachEvent('onmouseover', qf_AdvAC.onItemMouseOver);      
      }
      else
      {
        hAnchor.addEventListener( 'click',     qf_AdvAC.onItemClick, false );
        hAnchor.addEventListener( 'mouseover', qf_AdvAC.onItemMouseOver, false );
      }
      hItem.appendChild(hAnchor);
      qf_AdvAC.list.appendChild(hItem);
    }
  }
  
  if (window.hItem)
  {
    qf_AdvAC.showList();
    if (qf_AdvAC.container)
      qf_AdvAC.container.style.height = hItem.offsetHeight * qf_AdvAC.config.listMaxItem + "px";
  }
  else
    qf_AdvAC.hideList();
}

qf_AdvAC.onItemClick = function(hEvent)
{
  var hItem = ( hEvent.srcElement ) ? hEvent.srcElement : hEvent.originalTarget;

  qf_AdvAC.setValue();
  qf_AdvAC.clearList();
  qf_AdvAC.hideList();
  hEvent.cancelBubble = true;
  hEvent.returnValue = false;
  return false;
}

qf_AdvAC.onItemMouseOver = function(hEvent)
{
  qf_AdvAC.unselectCurrent();
  qf_AdvAC.activeItem = ( hEvent.srcElement ) ? hEvent.srcElement : hEvent.originalTarget;
  
  // We could run this only onclick, but I think it's better that activeIndex is always synchronized
  var i = 0;
  while(qf_AdvAC.list.childNodes[i])
  {
    if (qf_AdvAC.list.childNodes[i] == qf_AdvAC.activeItem.parentNode)
    {
      qf_AdvAC.activeItemIdx = i;
      break;
    }
    i++;
  }
  
  if (qf_AdvAC.hideTimer != null) 
    clearTimeout(qf_AdvAC.hideTimer);
  qf_AdvAC.hideTimer = setTimeout("qf_AdvAC.hideList()", qf_AdvAC.config.hideTimeout);
  qf_AdvAC.selectCurrent();
}

qf_AdvAC.showList = function()
{
  if (!qf_AdvAC.container)
    return;
    
  qf_AdvAC.container.style.display = "block";
  if (qf_AdvAC.hideTimer != null) 
    clearTimeout(qf_AdvAC.hideTimer);
  qf_AdvAC.hideTimer = setTimeout("qf_AdvAC.hideList()", qf_AdvAC.config.hideTimeout);    
  qf_AdvAC.listShown = true;
}

qf_AdvAC.hideList = function()
{
  if (!qf_AdvAC.container)
    return;
  qf_AdvAC.container.style.display = "none";
  qf_AdvAC.listShown = false;
}

qf_AdvAC.selectCurrent = function()
{
  if (qf_AdvAC.activeItem)
  {
    qf_AdvAC.activeItem.style.backgroundColor = "highlight";
    qf_AdvAC.activeItem.style.color = "highlighttext";
    // Make sure the selected item is viewed
    if (qf_AdvAC.activeItemIdx > qf_AdvAC.windowMax)
      qf_AdvAC.scrollListDown();
    if (qf_AdvAC.activeItemIdx < qf_AdvAC.windowMin)
      qf_AdvAC.scrollListUp();
     
    //qf_AdvAC.debug.value = qf_AdvAC.activeItem.offsetTop + "-" + qf_AdvAC.container.scrollTop + "-" + qf_AdvAC.activeItemIdx;
  }
}

qf_AdvAC.scrollListDown = function()
{
  qf_AdvAC.container.scrollTop += qf_AdvAC.activeItem.offsetHeight;
  qf_AdvAC.windowMax++;
  qf_AdvAC.windowMin++;
}

qf_AdvAC.scrollListUp = function()
{
  qf_AdvAC.container.scrollTop -= qf_AdvAC.activeItem.offsetHeight;
  qf_AdvAC.windowMax--;
  qf_AdvAC.windowMin--;
}

qf_AdvAC.unselectCurrent = function()
{
  if (qf_AdvAC.activeItem)
  {
    qf_AdvAC.activeItem.style.backgroundColor = "";
    qf_AdvAC.activeItem.style.color = "black";
    qf_AdvAC.activeItem.className = "";
  }
}

qf_AdvAC.clearList = function ()
{
  while(qf_AdvAC.list.hasChildNodes())
    qf_AdvAC.list.removeChild(qf_AdvAC.list.childNodes[0]);
}

qf_AdvAC.createContainer = function()
{
  if (qf_AdvAC.container)
  {
    qf_AdvAC.container.parentNode.removeChild(qf_AdvAC.container);
    qf_AdvAC.container = null;
    qf_AdvAC.list = null;
  }

  hC = document.createElement("div");
  hC.style.width = qf_AdvAC.input.offsetWidth + "px";
  
  var nTop  = qf_AdvAC.getOffsetParam(qf_AdvAC.input, 'offsetTop');
  var nLeft = qf_AdvAC.getOffsetParam(qf_AdvAC.input, 'offsetLeft');

  hC.style.position   = "absolute";
  hC.style.top        = (nTop + qf_AdvAC.input.offsetHeight) + 'px';
  hC.style.left       = nLeft + 'px';
  
  hC.style.zIndex     = "10000";
  
  hC.className = "qf_AdvACContainer";

  hC.style.display    = 'none';
  hC.style.visibility = 'visible';
  hList = document.createElement("ul");
  hList.className = "qf_AdvACList";
  hC.appendChild(hList);
  document.body.appendChild(hC);
  qf_AdvAC.container = hC;
  qf_AdvAC.list = hList;
}

function qf_AdvACConfiguration()
{
  this.hideTimeout = 0;
}

function QF_AdvAutocomplete_Focus(hInput, list) {
	// Turn off browser autocomplete
	hInput.autocomplete = 'off';

  // Create qf_AdvAC object	
  qf_AdvAC.config = new qf_AdvACConfiguration();
  qf_AdvAC.configure();
  qf_AdvAC.plugin = list;  
  qf_AdvAC.input = hInput;

  qf_AdvAC.searchBeginsWith = "1";
  qf_AdvAC.searchCacheLimit = 0;

  // first, remove the event handler if any (it is mandatory for IE to work well)
  if (hInput.attachEvent)
  {
    hInput.detachEvent('onkeyup',   qf_AdvAC.onInputKeyUp);
    hInput.detachEvent('onkeydown', qf_AdvAC.onInputKeyDown);
    hInput.detachEvent('onblur',    qf_AdvAC.onInputBlur);
    hInput.attachEvent('onkeyup',   qf_AdvAC.onInputKeyUp);
    hInput.attachEvent('onkeydown', qf_AdvAC.onInputKeyDown);
    hInput.attachEvent('onblur',    qf_AdvAC.onInputBlur);
  }
  else 
  if (hInput.addEventListener)
  {
    hInput.removeEventListener('keyup',   qf_AdvAC.onInputKeyUp,   false);
    hInput.removeEventListener('keydown', qf_AdvAC.onInputKeyDown, false);
    hInput.removeEventListener('blur',    qf_AdvAC.onInputBlur,    false);
    hInput.addEventListener('keyup',   qf_AdvAC.onInputKeyUp,   false);
    hInput.addEventListener('keydown', qf_AdvAC.onInputKeyDown, false);
    hInput.addEventListener('blur',    qf_AdvAC.onInputBlur,    false);
  }

  if (hInput.form)
  {
    if (hInput.form.attachEvent)
      hInput.form.attachEvent( 'onsubmit', qf_AdvAC.onFormSubmit )
    else 
     if ( hInput.form.addEventListener )
       hInput.form.addEventListener( 'submit', qf_AdvAC.onFormSubmit, false )
  }
    
  qf_AdvAC.createContainer();
}

//
//  This script was created
//  by Mircho Mirev
//  mo /mo@momche.net/
qf_AdvAC.getOffsetParam = function( hElement, sParam, hLimitParent )
{
  var nRes = 0
  if (hLimitParent == null)
  {
    hLimitParent = document.body.parentElement
  }
  while (hElement != hLimitParent)
  {
    nRes += eval( 'hElement.' + sParam )
    if( !hElement.offsetParent ) { break }
    hElement = hElement.offsetParent
  }
  return nRes;
}

// From
// http://www.developingskills.com/ds.php?article=jstrim&page=1
function strtrim() 
{
  return this.replace(/^\s+/,'').replace(/\s+$/,'');
}

String.prototype.trim = strtrim;

function qf_AdvAC()
{
}
// end javascript for AdvAutocomplete

EOS;
                define('HTML_QUICKFORM_ADVAUTOCOMPLETE_EXISTS', true);
	    }
	    $jsEscape = array(
                "\r"    => '\r',
                "\n"    => '\n',
                "\t"    => '\t',
                "'"     => "\\'",
                '"'     => '\"',
                '\\'    => '\\\\'
            );	    $js .= 'var ' . $arrayName . " = new Array();\n";
	    for ($i = 0; $i < count($this->_options); $i++) {
		$js .= $arrayName . '[' . $i . "] = '" . strtr($this->_options[$i], $jsEscape) . "';\n";
	    }
	    $js .= "//]]>\n</script>\n";
            return $js . parent::toHtml();
        }
    } //end func toHtml

    // }}}
} //end class HTML_QuickForm_SelectFilter

if (class_exists('HTML_QuickForm')) {
    HTML_QuickForm::registerElementType('AdvAutocomplete', 'AdvAutocomplete.php', 'HTML_QuickForm_AdvAutocomplete');
}
?>