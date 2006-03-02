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
class HTML_QuickForm_AJAXAutocomplete extends HTML_QuickForm_text {
    
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
    function HTML_QuickForm_AJAXAutocomplete($elementName = null, $elementLabel = null, $attributes = null)
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
    // {{{ setDatasource()

    /**
     * Sets what script to use to get data and what extra data to send it
     * 
     * @param     string    $sourceUrl   URL for ajax information
     * @param     mixed     $extras      (optional)Extra form fields to send to the data request 
     *
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setDatasource($sourceUrl, $extras = array())
    {
        $this->sourceUrl = $sourceUrl;
	$this->extraData = $extras;
    } //end func setDatasource

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
	$onFocus = 'javascript:QF_AJAXAutocomplete_Focus(this, "' . $this->sourceUrl . '" , '. $arrayName .');';

        $this->updateAttributes(array('onfocus' => $onFocus));

        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } else {
            $tabs = $this->_getTabs();
            $js = '<script type="text/javascript">';
	    $js .= "\n";
	    $js .= '//<![CDATA[';
	    $js .= "\n";
	    if (!defined('HTML_QUICKFORM_AJAXAUTOCOMPLETE_EXISTS')) {
                $js  .= <<< EOS
// begin javascript for filtered select
qf_AJAXAC.configure = function()
{
  // Timeout (in ms) after which the list is automatically hidden
  qf_AJAXAC.config.hideTimeout     = 15000;
  // Timeout (in ms) after which the search is began
  qf_AJAXAC.config.acTimeout       = 250;
  // How many items have to stay visibile in the list
  qf_AJAXAC.config.listMaxItem     = 10;
  // How many characters are required before auto complete
  qf_AJAXAC.config.minchar = 0;
}

qf_AJAXAC.onInputKeyUp = function ( hEvent )
{
  if (qf_AJAXAC.hideTimer != null) 
    clearTimeout(qf_AJAXAC.hideTimer);
    
  qf_AJAXAC.hideTimer = setTimeout("qf_AJAXAC.hideList()", qf_AJAXAC.config.hideTimeout);
  
  if (qf_AJAXAC.input.value.length < qf_AJAXAC.config.minchar && qf_AJAXAC.listShown)
    qf_AJAXAC.hideList();      
}

qf_AJAXAC.onInputKeyDown = function ( hEvent )
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
      qf_AJAXAC.setValue();
      qf_AJAXAC.clearList();
      qf_AJAXAC.hideList();
      hEvent.returnValue = false;
      break;
      
    case 40: // Down 
      if (!qf_AJAXAC.listShown)
      {
        if (qf_AJAXAC.acTimer != null) 
          clearTimeout(qf_AJAXAC.acTimer);
        qf_AJAXAC.autoComplete();    
        qf_AJAXAC.selectCurrent();
        break;
      }

      if (qf_AJAXAC.container && qf_AJAXAC.list.hasChildNodes())
      {
        if (qf_AJAXAC.activeItem == null)
        {
          try {
            qf_AJAXAC.activeItem = qf_AJAXAC.list.childNodes.item(0).firstChild;
            qf_AJAXAC.activeItemIdx = 0;
            qf_AJAXAC.windowMax = qf_AJAXAC.config.listMaxItem - 1;
            qf_AJAXAC.windowMin = 0;
          }
          catch(e) {}
        }
        else
        {
          try {
            nextItem = qf_AJAXAC.list.childNodes.item(qf_AJAXAC.activeItemIdx + 1).firstChild;
            qf_AJAXAC.unselectCurrent();
            qf_AJAXAC.activeItem = nextItem;
            qf_AJAXAC.activeItemIdx++;
          } 
          catch(e) {}
        }
      }
      qf_AJAXAC.selectCurrent();
      break;

    case 37: // Left
      break;
              
    case 38: // Up
      if (!qf_AJAXAC.listShown)
        break;
        
      if (qf_AJAXAC.container && qf_AJAXAC.list.hasChildNodes())
      {
        if (qf_AJAXAC.activeItem != null)
        {
          qf_AJAXAC.unselectCurrent();
          if (qf_AJAXAC.activeItemIdx > 0)
          {
            qf_AJAXAC.activeItemIdx--;
            qf_AJAXAC.activeItem = qf_AJAXAC.list.childNodes.item(qf_AJAXAC.activeItemIdx).firstChild;
          }
        }
      }
      qf_AJAXAC.selectCurrent();
      break;

    case 27: // Esc
      if (!qf_AJAXAC.listShown)
        break;
      qf_AJAXAC.clearList();
      qf_AJAXAC.hideList();
      break;
      
    case 9: // TAB
      qf_AJAXAC.hideList();
      qf_AJAXAC.onInputBlur();
      break;
                        
    default:  
      if (qf_AJAXAC.acTimer != null) 
        clearTimeout(qf_AJAXAC.acTimer);
      qf_AJAXAC.acTimer = setTimeout("qf_AJAXAC.autoComplete()", qf_AJAXAC.config.acTimeout);    
      break;
  }
  
  return hEvent.returnValue;
}

qf_AJAXAC.setValue = function ()
{
  if (qf_AJAXAC.activeItem)
  {
    qf_AJAXAC.input.value = qf_AJAXAC.activeItem.innerHTML.replace(/<strong>(.*)<\/strong>/ig, "$1");
  }
}

qf_AJAXAC.getMatchingElementKey = function(idx)
{
  var i=0;
  for (match in qf_AJAXAC.matches)
  {
    if (i == idx)
      return match;
    i++;
  }
}

qf_AJAXAC.onFormSubmit = function (hEvent)
{
  if( hEvent == null )
    hEvent = window.event;

      qf_AJAXAC.clearList();
      qf_AJAXAC.hideList();
      if (qf_AJAXAC.acTimer != null) 
        clearTimeout(qf_AJAXAC.acTimer);
}

qf_AJAXAC.onInputBlur = function ( hEvent )
{
  if (qf_AJAXAC.listShown)
    return;
    
  delete qf_AJAXAC.activeItem;
  qf_AJAXAC.activeItemIdx = 0;
  if (qf_AJAXAC.container)
  {
    qf_AJAXAC.clearList();
    qf_AJAXAC.container.parentNode.removeChild(qf_AJAXAC.container);
    qf_AJAXAC.container = null;
  }
}

qf_AJAXAC.autoComplete = function ()
{
  var pattern = qf_AJAXAC.input.value.toLowerCase().trim();
  if (pattern.length < qf_AJAXAC.config.minchar)
    return;

  if (qf_AJAXAC.xhr)
  {
    qf_AJAXAC.xhr.abort();
    delete qf_AJAXAC.xhr;
  }
    
  qf_AJAXAC.xhr = new XHConn();
  
  if (!qf_AJAXAC.xhr) alert("XMLHTTP not available. Try a newer/better browser.");

  if (qf_AJAXAC.cache)
    qf_AJAXAC.feedListFromCache();
  else
  {
    query  = "q="  + pattern;
    for (item in qf_AJAXAC.plugin)
    {
	    query += "&" + qf_AJAXAC.plugin[item] + "=" + eval("qf_AJAXAC.input.form." + qf_AJAXAC.plugin[item] + ".value");
    }

    if (!qf_AJAXAC.xhr.connect(qf_AJAXAC.dataurl, 
                            'GET', 
                            query,
                            qf_AJAXAC.feedListFromServer))
      alert("Failed connecting");
  }
}

qf_AJAXAC.feedListFromCache = function ()
{
  qf_AJAXAC.matches = qf_AJAXAC.cache;
  qf_AJAXAC.feedList();
}

qf_AJAXAC.feedListFromServer = function (data)
{
  if (data.status.toString() != "200")
  {
    alert("Can't connect [" + data.status.toString() + "]");
    return;
  }
    
  eval("qf_AJAXAC.matches = " + data.responseText);

  if (!qf_AJAXAC.matches)
  {
    qf_AJAXAC.hideList();
    return;
  }
  
  if (qf_AJAXAC.searchCacheLimit > 0)
    qf_AJAXAC.cache = qf_AJAXAC.matches;
  
  qf_AJAXAC.feedList();
}

qf_AJAXAC.feedList = function()
{
  qf_AJAXAC.clearList();
        
  qf_AJAXAC.activeItem = null;
  qf_AJAXAC.activeItemIdx = 0;
  
  var pattern = qf_AJAXAC.input.value.toLowerCase().trim();
  hItem = null;
  for (match in qf_AJAXAC.matches)
  {
    idx = qf_AJAXAC.matches[match].toLowerCase().indexOf(pattern);

    if (idx != -1 || qf_AJAXAC.matches[match] == "???")
    {
      hItem = document.createElement("li");
      hAnchor = document.createElement("a");
      hAnchor.href="#";
      hAnchor.innerHTML  = qf_AJAXAC.matches[match].substring(0, idx) + "<strong >" + qf_AJAXAC.matches[match].substring(idx, idx + pattern.length) + "</strong >"  + qf_AJAXAC.matches[match].substring(idx + pattern.length);
      
      if (hAnchor.attachEvent) 
      {
        hAnchor.attachEvent('onclick',     qf_AJAXAC.onItemClick);
        hAnchor.attachEvent('onmouseover', qf_AJAXAC.onItemMouseOver);      
      }
      else
      {
        hAnchor.addEventListener( 'click',     qf_AJAXAC.onItemClick, false );
        hAnchor.addEventListener( 'mouseover', qf_AJAXAC.onItemMouseOver, false );
      }
      hItem.appendChild(hAnchor);
      qf_AJAXAC.list.appendChild(hItem);
    }
  }
  
  if (window.hItem)
  {
    qf_AJAXAC.showList();
    if (qf_AJAXAC.container)
      qf_AJAXAC.container.style.height = hItem.offsetHeight * qf_AJAXAC.config.listMaxItem + "px";
  }
  else
    qf_AJAXAC.hideList();
}

qf_AJAXAC.onItemClick = function(hEvent)
{
  var hItem = ( hEvent.srcElement ) ? hEvent.srcElement : hEvent.originalTarget;

  qf_AJAXAC.setValue();
  qf_AJAXAC.clearList();
  qf_AJAXAC.hideList();
  hEvent.cancelBubble = true;
  hEvent.returnValue = false;
  return false;
}

qf_AJAXAC.onItemMouseOver = function(hEvent)
{
  qf_AJAXAC.unselectCurrent();
  qf_AJAXAC.activeItem = ( hEvent.srcElement ) ? hEvent.srcElement : hEvent.originalTarget;
  
  // We could run this only onclick, but I think it's better that activeIndex is always synchronized
  var i = 0;
  while(qf_AJAXAC.list.childNodes[i])
  {
    if (qf_AJAXAC.list.childNodes[i] == qf_AJAXAC.activeItem.parentNode)
    {
      qf_AJAXAC.activeItemIdx = i;
      break;
    }
    i++;
  }
  
  if (qf_AJAXAC.hideTimer != null) 
    clearTimeout(qf_AJAXAC.hideTimer);
  qf_AJAXAC.hideTimer = setTimeout("qf_AJAXAC.hideList()", qf_AJAXAC.config.hideTimeout);
  qf_AJAXAC.selectCurrent();
}

qf_AJAXAC.showList = function()
{
  if (!qf_AJAXAC.container)
    return;
    
  qf_AJAXAC.container.style.display = "block";
  if (qf_AJAXAC.hideTimer != null) 
    clearTimeout(qf_AJAXAC.hideTimer);
  qf_AJAXAC.hideTimer = setTimeout("qf_AJAXAC.hideList()", qf_AJAXAC.config.hideTimeout);    
  qf_AJAXAC.listShown = true;
}

qf_AJAXAC.hideList = function()
{
  if (!qf_AJAXAC.container)
    return;
  qf_AJAXAC.container.style.display = "none";
  qf_AJAXAC.listShown = false;
}

qf_AJAXAC.selectCurrent = function()
{
  if (qf_AJAXAC.activeItem)
  {
    qf_AJAXAC.activeItem.style.backgroundColor = "highlight";
    qf_AJAXAC.activeItem.style.color = "highlighttext";
    // Make sure the selected item is viewed
    if (qf_AJAXAC.activeItemIdx > qf_AJAXAC.windowMax)
      qf_AJAXAC.scrollListDown();
    if (qf_AJAXAC.activeItemIdx < qf_AJAXAC.windowMin)
      qf_AJAXAC.scrollListUp();
     
    //qf_AJAXAC.debug.value = qf_AJAXAC.activeItem.offsetTop + "-" + qf_AJAXAC.container.scrollTop + "-" + qf_AJAXAC.activeItemIdx;
  }
}

qf_AJAXAC.scrollListDown = function()
{
  qf_AJAXAC.container.scrollTop += qf_AJAXAC.activeItem.offsetHeight;
  qf_AJAXAC.windowMax++;
  qf_AJAXAC.windowMin++;
}

qf_AJAXAC.scrollListUp = function()
{
  qf_AJAXAC.container.scrollTop -= qf_AJAXAC.activeItem.offsetHeight;
  qf_AJAXAC.windowMax--;
  qf_AJAXAC.windowMin--;
}

qf_AJAXAC.unselectCurrent = function()
{
  if (qf_AJAXAC.activeItem)
  {
    qf_AJAXAC.activeItem.style.backgroundColor = "";
    qf_AJAXAC.activeItem.style.color = "black";
    qf_AJAXAC.activeItem.className = "";
  }
}

qf_AJAXAC.clearList = function ()
{
  while(qf_AJAXAC.list.hasChildNodes())
    qf_AJAXAC.list.removeChild(qf_AJAXAC.list.childNodes[0]);
}

qf_AJAXAC.createContainer = function()
{
  if (qf_AJAXAC.container)
  {
    qf_AJAXAC.container.parentNode.removeChild(qf_AJAXAC.container);
    qf_AJAXAC.container = null;
    qf_AJAXAC.list = null;
  }

  hC = document.createElement("div");
  hC.style.width = qf_AJAXAC.input.offsetWidth + "px";
  
  var nTop  = qf_AJAXAC.getOffsetParam(qf_AJAXAC.input, 'offsetTop');
  var nLeft = qf_AJAXAC.getOffsetParam(qf_AJAXAC.input, 'offsetLeft');

  hC.style.position   = "absolute";
  hC.style.top        = (nTop + qf_AJAXAC.input.offsetHeight) + 'px';
  hC.style.left       = nLeft + 'px';
  
  hC.style.zIndex     = "10000";
  
  hC.className = "qf_AdvACContainer";

  hC.style.display    = 'none';
  hC.style.visibility = 'visible';
  hList = document.createElement("ul");
  hList.className = "qf_AdvACList";
  hC.appendChild(hList);
  document.body.appendChild(hC);
  qf_AJAXAC.container = hC;
  qf_AJAXAC.list = hList;
}

function qf_AJAXACConfiguration()
{
  this.hideTimeout = 0;
}

function QF_AJAXAutocomplete_Focus(hInput, url, list) {
	// Turn off browser autocomplete
	hInput.autocomplete = 'off';

  // Create qf_AJAXAC object	
  qf_AJAXAC.config = new qf_AJAXACConfiguration();
  qf_AJAXAC.configure();
  qf_AJAXAC.dataurl = url;
  qf_AJAXAC.plugin = list;  
  qf_AJAXAC.input = hInput;

  qf_AJAXAC.searchBeginsWith = "1";
  qf_AJAXAC.searchCacheLimit = 0;

  // first, remove the event handler if any (it is mandatory for IE to work well)
  if (hInput.attachEvent)
  {
    hInput.detachEvent('onkeyup',   qf_AJAXAC.onInputKeyUp);
    hInput.detachEvent('onkeydown', qf_AJAXAC.onInputKeyDown);
    hInput.detachEvent('onblur',    qf_AJAXAC.onInputBlur);
    hInput.attachEvent('onkeyup',   qf_AJAXAC.onInputKeyUp);
    hInput.attachEvent('onkeydown', qf_AJAXAC.onInputKeyDown);
    hInput.attachEvent('onblur',    qf_AJAXAC.onInputBlur);
  }
  else 
  if (hInput.addEventListener)
  {
    hInput.removeEventListener('keyup',   qf_AJAXAC.onInputKeyUp,   false);
    hInput.removeEventListener('keydown', qf_AJAXAC.onInputKeyDown, false);
    hInput.removeEventListener('blur',    qf_AJAXAC.onInputBlur,    false);
    hInput.addEventListener('keyup',   qf_AJAXAC.onInputKeyUp,   false);
    hInput.addEventListener('keydown', qf_AJAXAC.onInputKeyDown, false);
    hInput.addEventListener('blur',    qf_AJAXAC.onInputBlur,    false);
  }

  if (hInput.form)
  {
    if (hInput.form.attachEvent)
      hInput.form.attachEvent( 'onsubmit', qf_AJAXAC.onFormSubmit )
    else 
     if ( hInput.form.addEventListener )
       hInput.form.addEventListener( 'submit', qf_AJAXAC.onFormSubmit, false )
  }
    
  qf_AJAXAC.createContainer();
}

//
//  This script was created
//  by Mircho Mirev
//  mo /mo@momche.net/
qf_AJAXAC.getOffsetParam = function( hElement, sParam, hLimitParent )
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

/** XHConn - Simple XMLHTTP Interface - brad@xkr.us - 2005-01-24             **
 ** Code licensed under Creative Commons Attribution-ShareAlike License      **
 ** http://creativecommons.org/licenses/by-sa/2.0/                           **/
function XHConn()
{
  var xmlhttp;
  var active;
  try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); }
  catch (e) { try { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }
  catch (e) { try { xmlhttp = new XMLHttpRequest(); }
  catch (e) { xmlhttp = false; }}}
  if (!xmlhttp) return null;
  this.connect = function(sURL, sMethod, sVars, fnDone)
  {
    if (!xmlhttp) return false;
    sMethod = sMethod.toUpperCase();

    try {
      if (sMethod == "GET")
      {
        xmlhttp.open(sMethod, sURL+"?"+sVars, true);
        sVars = "";
      }
      else
      {
        xmlhttp.open(sMethod, sURL, true);
        xmlhttp.setRequestHeader("Method", "POST "+sURL+" HTTP/1.1");
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      }
      xmlhttp.onreadystatechange = function(){ if (xmlhttp.readyState == 4) {
        fnDone(xmlhttp); }};
      xmlhttp.send(sVars);
    }
    catch(z) { return false; }
    return true;
  };
  this.abort = function()
  {
    try {
      //xmlhttp.abort();
    }
    catch(z) { return false; }
  }
  
  return this;
}

// From
// http://www.developingskills.com/ds.php?article=jstrim&page=1
function strtrim() 
{
  return this.replace(/^\s+/,'').replace(/\s+$/,'');
}

String.prototype.trim = strtrim;

function qf_AJAXAC()
{
}
// end javascript for AJAXAutocomplete

EOS;
                define('HTML_QUICKFORM_AJAXAUTOCOMPLETE_EXISTS', true);
	    }
	    $jsEscape = array(
                "\r"    => '\r',
                "\n"    => '\n',
                "\t"    => '\t',
                "'"     => "\\'",
                '"'     => '\"',
                '\\'    => '\\\\'
            );	    $js .= 'var ' . $arrayName . " = new Array();\n";
	    for ($i = 0; $i < count($this->extraData); $i++) {
		$js .= $arrayName . '[' . $i . "] = '" . strtr($this->extraData[$i], $jsEscape) . "';\n";
	    }
	    $js .= "//]]>\n</script>\n";
            return $js . parent::toHtml();
        }
    } //end func toHtml

    // }}}
} //end class HTML_QuickForm_SelectFilter

if (class_exists('HTML_QuickForm')) {
    HTML_QuickForm::registerElementType('AJAXAutocomplete', 'AJAXAutocomplete.php', 'HTML_QuickForm_AJAXAutocomplete');
}
?>