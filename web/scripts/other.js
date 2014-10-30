/*
    http://www.JSON.org/json2.js
    2011-10-19

    Public Domain.

    NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.

    See http://www.JSON.org/js.html


    This code should be minified before deployment.
    See http://javascript.crockford.com/jsmin.html

    USE YOUR OWN COPY. IT IS EXTREMELY UNWISE TO LOAD CODE FROM SERVERS YOU DO
    NOT CONTROL.


    This file creates a global JSON object containing two methods: stringify
    and parse.

        JSON.stringify(value, replacer, space)
            value       any JavaScript value, usually an object or array.

            replacer    an optional parameter that determines how object
                        values are stringified for objects. It can be a
                        function or an array of strings.

            space       an optional parameter that specifies the indentation
                        of nested structures. If it is omitted, the text will
                        be packed without extra whitespace. If it is a number,
                        it will specify the number of spaces to indent at each
                        level. If it is a string (such as '\t' or '&nbsp;'),
                        it contains the characters used to indent at each level.

            This method produces a JSON text from a JavaScript value.

            When an object value is found, if the object contains a toJSON
            method, its toJSON method will be called and the result will be
            stringified. A toJSON method does not serialize: it returns the
            value represented by the name/value pair that should be serialized,
            or undefined if nothing should be serialized. The toJSON method
            will be passed the key associated with the value, and this will be
            bound to the value

            For example, this would serialize Dates as ISO strings.

                Date.prototype.toJSON = function (key) {
                    function f(n) {
                        // Format integers to have at least two digits.
                        return n < 10 ? '0' + n : n;
                    }

                    return this.getUTCFullYear()   + '-' +
                         f(this.getUTCMonth() + 1) + '-' +
                         f(this.getUTCDate())      + 'T' +
                         f(this.getUTCHours())     + ':' +
                         f(this.getUTCMinutes())   + ':' +
                         f(this.getUTCSeconds())   + 'Z';
                };

            You can provide an optional replacer method. It will be passed the
            key and value of each member, with this bound to the containing
            object. The value that is returned from your method will be
            serialized. If your method returns undefined, then the member will
            be excluded from the serialization.

            If the replacer parameter is an array of strings, then it will be
            used to select the members to be serialized. It filters the results
            such that only members with keys listed in the replacer array are
            stringified.

            Values that do not have JSON representations, such as undefined or
            functions, will not be serialized. Such values in objects will be
            dropped; in arrays they will be replaced with null. You can use
            a replacer function to replace those with JSON values.
            JSON.stringify(undefined) returns undefined.

            The optional space parameter produces a stringification of the
            value that is filled with line breaks and indentation to make it
            easier to read.

            If the space parameter is a non-empty string, then that string will
            be used for indentation. If the space parameter is a number, then
            the indentation will be that many spaces.

            Example:

            text = JSON.stringify(['e', {pluribus: 'unum'}]);
            // text is '["e",{"pluribus":"unum"}]'


            text = JSON.stringify(['e', {pluribus: 'unum'}], null, '\t');
            // text is '[\n\t"e",\n\t{\n\t\t"pluribus": "unum"\n\t}\n]'

            text = JSON.stringify([new Date()], function (key, value) {
                return this[key] instanceof Date ?
                    'Date(' + this[key] + ')' : value;
            });
            // text is '["Date(---current time---)"]'


        JSON.parse(text, reviver)
            This method parses a JSON text to produce an object or array.
            It can throw a SyntaxError exception.

            The optional reviver parameter is a function that can filter and
            transform the results. It receives each of the keys and values,
            and its return value is used instead of the original value.
            If it returns what it received, then the structure is not modified.
            If it returns undefined then the member is deleted.

            Example:

            // Parse the text. Values that look like ISO date strings will
            // be converted to Date objects.

            myData = JSON.parse(text, function (key, value) {
                var a;
                if (typeof value === 'string') {
                    a =
/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2}(?:\.\d*)?)Z$/.exec(value);
                    if (a) {
                        return new Date(Date.UTC(+a[1], +a[2] - 1, +a[3], +a[4],
                            +a[5], +a[6]));
                    }
                }
                return value;
            });

            myData = JSON.parse('["Date(09/09/2001)"]', function (key, value) {
                var d;
                if (typeof value === 'string' &&
                        value.slice(0, 5) === 'Date(' &&
                        value.slice(-1) === ')') {
                    d = new Date(value.slice(5, -1));
                    if (d) {
                        return d;
                    }
                }
                return value;
            });


    This is a reference implementation. You are free to copy, modify, or
    redistribute.
*/

/*jslint evil: true, regexp: true */

/*members "", "\b", "\t", "\n", "\f", "\r", "\"", JSON, "\\", apply,
    call, charCodeAt, getUTCDate, getUTCFullYear, getUTCHours,
    getUTCMinutes, getUTCMonth, getUTCSeconds, hasOwnProperty, join,
    lastIndex, length, parse, prototype, push, replace, slice, stringify,
    test, toJSON, toString, valueOf
*/


// Create a JSON object only if one does not already exist. We create the
// methods in a closure to avoid creating global variables.

var JSON;
if (!JSON) {
    JSON = {};
}

(function () {
    'use strict';

    function f(n) {
        // Format integers to have at least two digits.
        return n < 10 ? '0' + n : n;
    }

    if (typeof Date.prototype.toJSON !== 'function') {

        Date.prototype.toJSON = function (key) {

            return isFinite(this.valueOf())
                ? this.getUTCFullYear()     + '-' +
                    f(this.getUTCMonth() + 1) + '-' +
                    f(this.getUTCDate())      + 'T' +
                    f(this.getUTCHours())     + ':' +
                    f(this.getUTCMinutes())   + ':' +
                    f(this.getUTCSeconds())   + 'Z'
                : null;
        };

        String.prototype.toJSON      =
            Number.prototype.toJSON  =
            Boolean.prototype.toJSON = function (key) {
                return this.valueOf();
            };
    }

    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        gap,
        indent,
        meta = {    // table of character substitutions
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        },
        rep;


    function quote(string) {

// If the string contains no control characters, no quote characters, and no
// backslash characters, then we can safely slap some quotes around it.
// Otherwise we must also replace the offending characters with safe escape
// sequences.

        escapable.lastIndex = 0;
        return escapable.test(string) ? '"' + string.replace(escapable, function (a) {
            var c = meta[a];
            return typeof c === 'string'
                ? c
                : '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
        }) + '"' : '"' + string + '"';
    }


    function str(key, holder) {

// Produce a string from holder[key].

        var i,          // The loop counter.
            k,          // The member key.
            v,          // The member value.
            length,
            mind = gap,
            partial,
            value = holder[key];

// If the value has a toJSON method, call it to obtain a replacement value.

        if (value && typeof value === 'object' &&
                typeof value.toJSON === 'function') {
            value = value.toJSON(key);
        }

// If we were called with a replacer function, then call the replacer to
// obtain a replacement value.

        if (typeof rep === 'function') {
            value = rep.call(holder, key, value);
        }

// What happens next depends on the value's type.

        switch (typeof value) {
        case 'string':
            return quote(value);

        case 'number':

// JSON numbers must be finite. Encode non-finite numbers as null.

            return isFinite(value) ? String(value) : 'null';

        case 'boolean':
        case 'null':

// If the value is a boolean or null, convert it to a string. Note:
// typeof null does not produce 'null'. The case is included here in
// the remote chance that this gets fixed someday.

            return String(value);

// If the type is 'object', we might be dealing with an object or an array or
// null.

        case 'object':

// Due to a specification blunder in ECMAScript, typeof null is 'object',
// so watch out for that case.

            if (!value) {
                return 'null';
            }

// Make an array to hold the partial results of stringifying this object value.

            gap += indent;
            partial = [];

// Is the value an array?

            if (Object.prototype.toString.apply(value) === '[object Array]') {

// The value is an array. Stringify every element. Use null as a placeholder
// for non-JSON values.

                length = value.length;
                for (i = 0; i < length; i += 1) {
                    partial[i] = str(i, value) || 'null';
                }

// Join all of the elements together, separated with commas, and wrap them in
// brackets.

                v = partial.length === 0
                    ? '[]'
                    : gap
                    ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']'
                    : '[' + partial.join(',') + ']';
                gap = mind;
                return v;
            }

// If the replacer is an array, use it to select the members to be stringified.

            if (rep && typeof rep === 'object') {
                length = rep.length;
                for (i = 0; i < length; i += 1) {
                    if (typeof rep[i] === 'string') {
                        k = rep[i];
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            } else {

// Otherwise, iterate through all of the keys in the object.

                for (k in value) {
                    if (Object.prototype.hasOwnProperty.call(value, k)) {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            }

// Join all of the member texts together, separated with commas,
// and wrap them in braces.

            v = partial.length === 0
                ? '{}'
                : gap
                ? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}'
                : '{' + partial.join(',') + '}';
            gap = mind;
            return v;
        }
    }

// If the JSON object does not yet have a stringify method, give it one.

    if (typeof JSON.stringify !== 'function') {
        JSON.stringify = function (value, replacer, space) {

// The stringify method takes a value and an optional replacer, and an optional
// space parameter, and returns a JSON text. The replacer can be a function
// that can replace values, or an array of strings that will select the keys.
// A default replacer method can be provided. Use of the space parameter can
// produce text that is more easily readable.

            var i;
            gap = '';
            indent = '';

// If the space parameter is a number, make an indent string containing that
// many spaces.

            if (typeof space === 'number') {
                for (i = 0; i < space; i += 1) {
                    indent += ' ';
                }

// If the space parameter is a string, it will be used as the indent string.

            } else if (typeof space === 'string') {
                indent = space;
            }

// If there is a replacer, it must be a function or an array.
// Otherwise, throw an error.

            rep = replacer;
            if (replacer && typeof replacer !== 'function' &&
                    (typeof replacer !== 'object' ||
                    typeof replacer.length !== 'number')) {
                throw new Error('JSON.stringify');
            }

// Make a fake root object containing our value under the key of ''.
// Return the result of stringifying the value.

            return str('', {'': value});
        };
    }


// If the JSON object does not yet have a parse method, give it one.

    if (typeof JSON.parse !== 'function') {
        JSON.parse = function (text, reviver) {

// The parse method takes a text and an optional reviver function, and returns
// a JavaScript value if the text is a valid JSON text.

            var j;

            function walk(holder, key) {

// The walk method is used to recursively walk the resulting structure so
// that modifications can be made.

                var k, v, value = holder[key];
                if (value && typeof value === 'object') {
                    for (k in value) {
                        if (Object.prototype.hasOwnProperty.call(value, k)) {
                            v = walk(value, k);
                            if (v !== undefined) {
                                value[k] = v;
                            } else {
                                delete value[k];
                            }
                        }
                    }
                }
                return reviver.call(holder, key, value);
            }


// Parsing happens in four stages. In the first stage, we replace certain
// Unicode characters with escape sequences. JavaScript handles many characters
// incorrectly, either silently deleting them, or treating them as line endings.

            text = String(text);
            cx.lastIndex = 0;
            if (cx.test(text)) {
                text = text.replace(cx, function (a) {
                    return '\\u' +
                        ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
                });
            }

// In the second stage, we run the text against regular expressions that look
// for non-JSON patterns. We are especially concerned with '()' and 'new'
// because they can cause invocation, and '=' because it can cause mutation.
// But just to be safe, we want to reject all unexpected forms.

// We split the second stage into 4 regexp operations in order to work around
// crippling inefficiencies in IE's and Safari's regexp engines. First we
// replace the JSON backslash pairs with '@' (a non-JSON character). Second, we
// replace all simple value tokens with ']' characters. Third, we delete all
// open brackets that follow a colon or comma or that begin the text. Finally,
// we look to see that the remaining characters are only whitespace or ']' or
// ',' or ':' or '{' or '}'. If that is so, then the text is safe for eval.

            if (/^[\],:{}\s]*$/
                    .test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@')
                        .replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']')
                        .replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {

// In the third stage we use the eval function to compile the text into a
// JavaScript structure. The '{' operator is subject to a syntactic ambiguity
// in JavaScript: it can begin a block or an object literal. We wrap the text
// in parens to eliminate the ambiguity.

                j = eval('(' + text + ')');

// In the optional fourth stage, we recursively walk the new structure, passing
// each name/value pair to a reviver function for possible transformation.

                return typeof reviver === 'function'
                    ? walk({'': j}, '')
                    : j;
            }

// If the text is not JSON parseable, then a SyntaxError is thrown.

            throw new SyntaxError('JSON.parse');
        };
    }
}());
/**
 * Copyright (c) 2010 Maxim Vasiliev
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Maxim Vasiliev
 * Date: 09.09.2010
 * Time: 19:02:33
 */


var form2js = (function()
{
	"use strict";

	/**
	 * Returns form values represented as Javascript object
	 * "name" attribute defines structure of resulting object
	 *
	 * @param rootNode {Element|String} root form element (or it's id) or array of root elements
	 * @param delimiter {String} structure parts delimiter defaults to '.'
	 * @param skipEmpty {Boolean} should skip empty text values, defaults to true
	 * @param nodeCallback {Function} custom function to get node value
	 * @param useIdIfEmptyName {Boolean} if true value of id attribute of field will be used if name of field is empty
	 */
	function form2js(rootNode, delimiter, skipEmpty, nodeCallback, useIdIfEmptyName)
	{
		if (typeof skipEmpty == 'undefined' || skipEmpty == null) skipEmpty = true;
		if (typeof delimiter == 'undefined' || delimiter == null) delimiter = '.';
		if (arguments.length < 5) useIdIfEmptyName = false;

		rootNode = typeof rootNode == 'string' ? document.getElementById(rootNode) : rootNode;

		var formValues = [],
			currNode,
			i = 0;

		/* If rootNode is array - combine values */
		if (rootNode.constructor == Array || (typeof NodeList != "undefined" && rootNode.constructor == NodeList))
		{
			while(currNode = rootNode[i++])
			{
				formValues = formValues.concat(getFormValues(currNode, nodeCallback, useIdIfEmptyName));
			}
		}
		else
		{
			formValues = getFormValues(rootNode, nodeCallback, useIdIfEmptyName);
		}

		return processNameValues(formValues, skipEmpty, delimiter);
	}

	/**
	 * Processes collection of { name: 'name', value: 'value' } objects.
	 * @param nameValues
	 * @param skipEmpty if true skips elements with value == '' or value == null
	 * @param delimiter
	 */
	function processNameValues(nameValues, skipEmpty, delimiter)
	{
		var result = {},
			arrays = {},
			i, j, k, l,
			value,
			nameParts,
			currResult,
			arrNameFull,
			arrName,
			arrIdx,
			namePart,
			name,
			_nameParts;

		for (i = 0; i < nameValues.length; i++)
		{
			value = nameValues[i].value;

			if (skipEmpty && (value === '' || value === null)) continue;

			name = nameValues[i].name;
			_nameParts = name.split(delimiter);
			nameParts = [];
			currResult = result;
			arrNameFull = '';

			for(j = 0; j < _nameParts.length; j++)
			{
				namePart = _nameParts[j].split('][');
				if (namePart.length > 1)
				{
					for(k = 0; k < namePart.length; k++)
					{
						if (k == 0)
						{
							namePart[k] = namePart[k] + ']';
						}
						else if (k == namePart.length - 1)
						{
							namePart[k] = '[' + namePart[k];
						}
						else
						{
							namePart[k] = '[' + namePart[k] + ']';
						}

						arrIdx = namePart[k].match(/([a-z_]+)?\[([a-z_][a-z0-9_]+?)\]/i);
						if (arrIdx)
						{
							for(l = 1; l < arrIdx.length; l++)
							{
								if (arrIdx[l]) nameParts.push(arrIdx[l]);
							}
						}
						else{
							nameParts.push(namePart[k]);
						}
					}
				}
				else
					nameParts = nameParts.concat(namePart);
			}

			for (j = 0; j < nameParts.length; j++)
			{
				namePart = nameParts[j];

				if (namePart.indexOf('[]') > -1 && j == nameParts.length - 1)
				{
					arrName = namePart.substr(0, namePart.indexOf('['));
					arrNameFull += arrName;

					if (!currResult[arrName]) currResult[arrName] = [];
					currResult[arrName].push(value);
				}
				else if (namePart.indexOf('[') > -1)
				{
					arrName = namePart.substr(0, namePart.indexOf('['));
					arrIdx = namePart.replace(/(^([a-z_]+)?\[)|(\]$)/gi, '');

					/* Unique array name */
					arrNameFull += '_' + arrName + '_' + arrIdx;

					/*
					 * Because arrIdx in field name can be not zero-based and step can be
					 * other than 1, we can't use them in target array directly.
					 * Instead we're making a hash where key is arrIdx and value is a reference to
					 * added array element
					 */

					if (!arrays[arrNameFull]) arrays[arrNameFull] = {};
					if (arrName != '' && !currResult[arrName]) currResult[arrName] = [];

					if (j == nameParts.length - 1)
					{
						if (arrName == '')
						{
							currResult.push(value);
							arrays[arrNameFull][arrIdx] = currResult[currResult.length - 1];
						}
						else
						{
							currResult[arrName].push(value);
							arrays[arrNameFull][arrIdx] = currResult[arrName][currResult[arrName].length - 1];
						}
					}
					else
					{
						if (!arrays[arrNameFull][arrIdx])
						{
							if ((/^[a-z_]+\[?/i).test(nameParts[j+1])) currResult[arrName].push({});
							else currResult[arrName].push([]);

							arrays[arrNameFull][arrIdx] = currResult[arrName][currResult[arrName].length - 1];
						}
					}

					currResult = arrays[arrNameFull][arrIdx];
				}
				else
				{
					arrNameFull += namePart;

					if (j < nameParts.length - 1) /* Not the last part of name - means object */
					{
						if (!currResult[namePart]) currResult[namePart] = {};
						currResult = currResult[namePart];
					}
					else
					{
						currResult[namePart] = value;
					}
				}
			}
		}

		return result;
	}

    function getFormValues(rootNode, nodeCallback, useIdIfEmptyName)
    {
        var result = extractNodeValues(rootNode, nodeCallback, useIdIfEmptyName);
        return result.length > 0 ? result : getSubFormValues(rootNode, nodeCallback, useIdIfEmptyName);
    }

    function getSubFormValues(rootNode, nodeCallback, useIdIfEmptyName)
	{
		var result = [],
			currentNode = rootNode.firstChild;
		
		while (currentNode)
		{
			result = result.concat(extractNodeValues(currentNode, nodeCallback, useIdIfEmptyName));
			currentNode = currentNode.nextSibling;
		}

		return result;
	}

    function extractNodeValues(node, nodeCallback, useIdIfEmptyName) {
        var callbackResult, fieldValue, result, fieldName = getFieldName(node, useIdIfEmptyName);

        callbackResult = nodeCallback && nodeCallback(node);

        if (callbackResult && callbackResult.name) {
            result = [callbackResult];
        }
        else if (fieldName != '' && node.nodeName.match(/INPUT|TEXTAREA/i)) {
            fieldValue = getFieldValue(node);
			result = [ { name: fieldName, value: fieldValue} ];
        }
        else if (fieldName != '' && node.nodeName.match(/SELECT/i)) {
	        fieldValue = getFieldValue(node);
	        result = [ { name: fieldName.replace(/\[\]$/, ''), value: fieldValue } ];
        }
        else {
            result = getSubFormValues(node, nodeCallback, useIdIfEmptyName);
        }

        return result;
    }

	function getFieldName(node, useIdIfEmptyName)
	{
		if (node.name && node.name != '') return node.name;
		else if (useIdIfEmptyName && node.id && node.id != '') return node.id;
		else return '';
	}


	function getFieldValue(fieldNode)
	{
		if (fieldNode.disabled) return null;
		
		switch (fieldNode.nodeName) {
			case 'INPUT':
			case 'TEXTAREA':
				switch (fieldNode.type.toLowerCase()) {
					case 'radio':
					case 'checkbox':
                        if (fieldNode.checked && fieldNode.value === "true") return true;
                        if (!fieldNode.checked && fieldNode.value === "true") return false;
						if (fieldNode.checked) return fieldNode.value;
						break;

					case 'button':
					case 'reset':
					case 'submit':
					case 'image':
						return '';
						break;

					default:
						return fieldNode.value;
						break;
				}
				break;

			case 'SELECT':
				return getSelectedOptionValue(fieldNode);
				break;

			default:
				break;
		}

		return null;
	}

	function getSelectedOptionValue(selectNode)
	{
		var multiple = selectNode.multiple,
			result = [],
			options,
			i, l;

		if (!multiple) return selectNode.value;

		for (options = selectNode.getElementsByTagName("option"), i = 0, l = options.length; i < l; i++)
		{
			if (options[i].selected) result.push(options[i].value);
		}

		return result;
	}

	return form2js;

})();
/**
 * Copyright (c) 2010 Maxim Vasiliev
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Maxim Vasiliev
 * Date: 19.09.11
 * Time: 23:40
 */

var js2form = (function()
{
	"use strict";

	var _subArrayRegexp = /^\[\d+?\]/,
			_subObjectRegexp = /^[a-zA-Z_][a-zA-Z_0-9]+/,
			_arrayItemRegexp = /\[[0-9]+?\]$/,
			_lastIndexedArrayRegexp = /(.*)(\[)([0-9]*)(\])$/,
			_arrayOfArraysRegexp = /\[([0-9]+)\]\[([0-9]+)\]/g,
			_inputOrTextareaRegexp = /INPUT|TEXTAREA/i;

	/**
	 *
	 * @param rootNode
	 * @param data
	 * @param delimiter
	 * @param nodeCallback
	 * @param useIdIfEmptyName
	 */
	function js2form(rootNode, data, delimiter, nodeCallback, useIdIfEmptyName)
	{
		if (arguments.length < 3) delimiter = '.';
		if (arguments.length < 4) nodeCallback = null;
		if (arguments.length < 5) useIdIfEmptyName = false;

		var fieldValues,
				formFieldsByName;

		fieldValues = object2array(data);
		formFieldsByName = getFields(rootNode, useIdIfEmptyName, delimiter, {}, true);

		for (var i = 0; i < fieldValues.length; i++)
		{
			var fieldName = fieldValues[i].name,
					fieldValue = fieldValues[i].value;

			if (typeof formFieldsByName[fieldName] != 'undefined')
			{
				setValue(formFieldsByName[fieldName], fieldValue);
			}
			else if (typeof formFieldsByName[fieldName.replace(_arrayItemRegexp, '[]')] != 'undefined')
			{
				setValue(formFieldsByName[fieldName.replace(_arrayItemRegexp, '[]')], fieldValue);
			}
		}
	}

	function setValue(field, value)
	{
		var children, i, l;

		if (field instanceof Array)
		{
			for(i = 0; i < field.length; i++)
			{
				if (field[i].value == value) field[i].checked = true;
			}
		}
		else if (_inputOrTextareaRegexp.test(field.nodeName))
		{
			field.value = value;
		}
		else if (/SELECT/i.test(field.nodeName))
		{
			children = field.getElementsByTagName('option');
			for (i = 0,l = children.length; i < l; i++)
			{
				if (children[i].value == value)
				{
					children[i].selected = true;
					if (field.multiple) break;
				}
				else if (!field.multiple)
				{
					children[i].selected = false;
				}
			}
		}
	}

	function getFields(rootNode, useIdIfEmptyName, delimiter, arrayIndexes, shouldClean)
	{
		if (arguments.length < 4) arrayIndexes = {};

		var result = {},
			currNode = rootNode.firstChild,
			name, nameNormalized,
			subFieldName,
			i, j, l,
			options;

		while (currNode)
		{
			name = '';

			if (currNode.name && currNode.name != '')
			{
				name = currNode.name;
			}
			else if (useIdIfEmptyName && currNode.id && currNode.id != '')
			{
				name = currNode.id;
			}

			if (name == '')
			{
				var subFields = getFields(currNode, useIdIfEmptyName, delimiter, arrayIndexes, shouldClean);
				for (subFieldName in subFields)
				{
					if (typeof result[subFieldName] == 'undefined')
					{
						result[subFieldName] = subFields[subFieldName];
					}
					else
					{
						for (i = 0; i < subFields[subFieldName].length; i++)
						{
							result[subFieldName].push(subFields[subFieldName][i]);
						}
					}
				}
			}
			else
			{
				if (/SELECT/i.test(currNode.nodeName))
				{
					for(j = 0, options = currNode.getElementsByTagName('option'), l = options.length; j < l; j++)
					{
						if (shouldClean)
						{
							options[j].selected = false;
						}

						nameNormalized = normalizeName(name, delimiter, arrayIndexes);
						result[nameNormalized] = currNode;
					}
				}
				else if (/INPUT/i.test(currNode.nodeName) && /CHECKBOX|RADIO/i.test(currNode.type))
				{
					if(shouldClean)
					{
						currNode.checked = false;
					}

					nameNormalized = normalizeName(name, delimiter, arrayIndexes);
					nameNormalized = nameNormalized.replace(_arrayItemRegexp, '[]');
					if (!result[nameNormalized]) result[nameNormalized] = [];
					result[nameNormalized].push(currNode);
				}
				else
				{
					if (shouldClean)
					{
						currNode.value = '';
					}

					nameNormalized = normalizeName(name, delimiter, arrayIndexes);
					result[nameNormalized] = currNode;
				}
			}

			currNode = currNode.nextSibling;
		}

		return result;
	}

	/**
	 * Normalizes names of arrays, puts correct indexes (consecutive and ordered by element appearance in HTML)
	 * @param name
	 * @param delimiter
	 * @param arrayIndexes
	 */
	function normalizeName(name, delimiter, arrayIndexes)
	{
		var nameChunksNormalized = [],
				nameChunks = name.split(delimiter),
				currChunk,
				nameMatches,
				nameNormalized,
				currIndex,
				newIndex,
				i;

		name = name.replace(_arrayOfArraysRegexp, '[$1].[$2]');
		for (i = 0; i < nameChunks.length; i++)
		{
			currChunk = nameChunks[i];
			nameChunksNormalized.push(currChunk);
			nameMatches = currChunk.match(_lastIndexedArrayRegexp);
			if (nameMatches != null)
			{
				nameNormalized = nameChunksNormalized.join(delimiter);
				currIndex = nameNormalized.replace(_lastIndexedArrayRegexp, '$3');
				nameNormalized = nameNormalized.replace(_lastIndexedArrayRegexp, '$1');

				if (typeof (arrayIndexes[nameNormalized]) == 'undefined')
				{
					arrayIndexes[nameNormalized] = {
						lastIndex: -1,
						indexes: {}
					};
				}

				if (currIndex == '' || typeof arrayIndexes[nameNormalized].indexes[currIndex] == 'undefined')
				{
					arrayIndexes[nameNormalized].lastIndex++;
					arrayIndexes[nameNormalized].indexes[currIndex] = arrayIndexes[nameNormalized].lastIndex;
				}

				newIndex = arrayIndexes[nameNormalized].indexes[currIndex];
				nameChunksNormalized[nameChunksNormalized.length - 1] = currChunk.replace(_lastIndexedArrayRegexp, '$1$2' + newIndex + '$4');
			}
		}

		nameNormalized = nameChunksNormalized.join(delimiter);
		nameNormalized = nameNormalized.replace('].[', '][');
		return nameNormalized;
	}

	function object2array(obj, lvl)
	{
		var result = [], i, name;

		if (arguments.length == 1) lvl = 0;

        if (obj == null)
        {
            result = [{ name: "", value: null }];
        }
        else if (typeof obj == 'string' || typeof obj == 'number' || typeof obj == 'date' || typeof obj == 'boolean')
        {
            result = [
                { name: "", value : obj }
            ];
        }
        else if (obj instanceof Array)
        {
            for (i = 0; i < obj.length; i++)
            {
                name = "[" + i + "]";
                result = result.concat(getSubValues(obj[i], name, lvl + 1));
            }
        }
        else
        {
            for (i in obj)
            {
                name = i;
                result = result.concat(getSubValues(obj[i], name, lvl + 1));
            }
        }

		return result;
    }

	function getSubValues(subObj, name, lvl)
	{
		var itemName;
		var result = [], tempResult = object2array(subObj, lvl + 1), i, tempItem;

		for (i = 0; i < tempResult.length; i++)
		{
			itemName = name;
			if (_subArrayRegexp.test(tempResult[i].name))
			{
				itemName += tempResult[i].name;
			}
			else if (_subObjectRegexp.test(tempResult[i].name))
			{
				itemName += '.' + tempResult[i].name;
			}

			tempItem = { name: itemName, value: tempResult[i].value };
			result.push(tempItem);
		}

		return result;
	}

	return js2form;

})();
/**
 * Copyright (c) 2010 Maxim Vasiliev
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Maxim Vasiliev
 * Date: 29.06.11
 * Time: 20:09
 */

(function($){

	/**
	 * jQuery wrapper for form2object()
	 * Extracts data from child inputs into javascript object
	 */
	$.fn.toObject = function(options)
	{
		var result = [],
			settings = {
				mode: 'first', // what to convert: 'all' or 'first' matched node
				delimiter: ".",
				skipEmpty: true,
				nodeCallback: null,
				useIdIfEmptyName: false
			};

		if (options)
		{
			$.extend(settings, options);
		}

		switch(settings.mode)
		{
			case 'first':
				return form2js(this.get(0), settings.delimiter, settings.skipEmpty, settings.nodeCallback, settings.useIdIfEmptyName);
				break;
			case 'all':
				this.each(function(){
					result.push(form2js(this, settings.delimiter, settings.skipEmpty, settings.nodeCallback, settings.useIdIfEmptyName));
				});
				return result;
				break;
			case 'combine':
				return form2js(Array.prototype.slice.call(this), settings.delimiter, settings.skipEmpty, settings.nodeCallback, settings.useIdIfEmptyName);
				break;
		}
	}

})(jQuery);
/**
 * jQuery jEC (jQuery Editable Combobox) 1.3.3
 * http://code.google.com/p/jquery-jec
 *
 * Copyright (c) 2008-2009 Lukasz Rajchel (lukasz@rajchel.pl | http://rajchel.pl)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * Documentation :  http://code.google.com/p/jquery-jec/wiki/Documentation
 * Changelog     :  http://code.google.com/p/jquery-jec/wiki/Changelog
 *
 * Contributors  :  Lukasz Rajchel, Artem Orlov
 */

/*jslint maxerr: 50, indent: 4, maxlen: 120*/
/*global Array, Math, String, clearInterval, document, jQuery, setInterval*/
/*members ':', Handle, Remove, Set, acceptedKeys, addClass, all, append, appendTo, array, attr, before, bind,
blinkingCursor, blinkingCursorInterval, blur, bool, browser, ceil, change, charCode, classes, clearCursor, click, css,
cursorState, data, destroy, disable, each, editable, enable, eq, expr, extend, filter, find, floor, fn, focus,
focusOnNewOption, fromCharCode, get, getId, handleCursor, ignoredKeys, ignoreOptGroups, inArray, init, initJS, integer,
isArray, isPlainObject, jEC, jECTimer, jec, jecKill, jecOff, jecOn, jecPref, jecValue, keyCode, keyDown, keyPress,
keyRange, keyUp, keys, length, max, maxLength, min, msie, object, openedState, optionClasses, optionStyles, parent,
position, pref, prop, push, random, remove, removeAttr, removeClass, removeData, removeProp, safari, setEditableOption,
styles, substring, text, trigger, triggerChangeEvent, unbind, uneditable, useExistingOptions, val, value,
valueIsEditable, which*/
(function ($) {
    'use strict';

    $.jEC = (function () {
        var pluginClass = 'jecEditableOption', cursorClass = 'hasCursor', options = {}, values = {}, lastKeyCode,
            defaults, Validators, EventHandlers, Combobox, activeCombobox;

        if ($.fn.prop === undefined) {
            $.fn.extend({
                'prop': function (key, valueSet) {
                    if (valueSet) {
                        $(this).attr(key, key);
                    } else {
                        $(this).removeAttr(key);
                    }
                },
                'removeProp': function (key) {
                    $(this).removeAttr(key);
                }
            });
        }

        defaults = {
            position: 0,
            ignoreOptGroups: false,
            maxLength: 255,
            classes: [],
            styles: {},
            optionClasses: [],
            optionStyles: {},
            triggerChangeEvent: false,
            focusOnNewOption: false,
            useExistingOptions: false,
            blinkingCursor: false,
            blinkingCursorInterval: 1000,
            ignoredKeys: [],
            acceptedKeys: [[32, 126], [191, 382]]
        };

        Validators = (function () {
            return {
                integer: function (value) {
                    return typeof value === 'number' && Math.ceil(value) === Math.floor(value);
                },

                keyRange: function (value) {
                    var min, max;
                    if ($.isPlainObject(value)) {
                        min = value.min;
                        max = value.max;
                    } else if ($.isArray(value) && value.length === 2) {
                        min = value[0];
                        max = value[1];
                    }
                    return Validators.integer(min) && Validators.integer(max) && min <= max;
                }
            };
        }());

        EventHandlers = (function () {
            var getKeyCode;

            getKeyCode = function (event) {
                var charCode = event.charCode;
                if (charCode !== undefined && charCode !== 0) {
                    return charCode;
                } else {
                    return event.keyCode;
                }
            };

            return {
                // focus event handler
                // enables blinking cursor
                focus: function () {
                    var opt = options[Combobox.getId($(this))];
                    if (opt.blinkingCursor && $.jECTimer === undefined) {
                        activeCombobox = $(this);
                        $.jECTimer = setInterval($.jEC.handleCursor, opt.blinkingCursorInterval);
                    }
                    Combobox.focusedState($(this), true);
                    $(this).data('jecFirstChar', true);
                },

                // blur event handler
                // disables blinking cursor
                blur: function () {
                    if ($.jECTimer !== undefined) {
                        clearInterval($.jECTimer);
                        $.jECTimer = undefined;
                        activeCombobox = undefined;
                        Combobox.clearCursor($(this));
                    }
                    Combobox.focusedState($(this), false);
                    Combobox.openedState($(this), false);
                    $(this).data('jecFirstChar', false);
                },

                // keydown event handler
                // handles keys pressed on select (backspace and delete must be handled
                // in keydown event in order to work in IE)
                keyDown: function (event) {
					var keyCode = getKeyCode(event), option, value;

                    lastKeyCode = keyCode;

                    switch (keyCode) {
                    case 8:  // backspace
                    case 46: // delete
						Combobox.focusedState($(this), false);
                        option = $(this).find('option.' + pluginClass);
                        if (option.val().length >= 1) {
                            value = option.text().substring(0, option.text().length - 1);
                            option.val(value).text(value).prop('selected', true);
                        }
                        return (keyCode !== 8);
                    default:
                        break;
                    }
                },

                // keypress event handler
                // handles the rest of the keys (keypress event gives more informations
                // about pressed keys)
                keyPress: function (event) {
                    var keyCode = getKeyCode(event), opt = options[Combobox.getId($(this))],
                        option, value, specialKeys, exit = false, text;
                    Combobox.clearCursor($(this));
                    if (keyCode !== 9 && keyCode !== 13 && keyCode !== 27) {
                        // special keys codes
                        specialKeys = [37, 38, 39, 40, 46];
                        // handle special keys
                        $.each(specialKeys, function (i, val) {
							if (keyCode === val && keyCode === lastKeyCode) {
								Combobox.focusedState($(this), false);
                                exit = true;
                            }
                        });

                        // don't handle ignored keys
                        if (!exit && $.inArray(keyCode, opt.ignoredKeys) === -1) {
                            // remove selection from all options
                            $(this).find('option:selected').removeProp('selected');

                            if ($.inArray(keyCode, opt.acceptedKeys) !== -1) {
                                option = $(this).find('option.' + pluginClass);
                                if ($(this).data('jecFirstChar'))
                                {
                                	text = '';
                                	Combobox.focusedState($(this), false);
                                	$(this).data('jecFirstChar', false);
								}
								else
								{
									text = option.text();
								}
								if (text.length < opt.maxLength) {
                                    value = text + String.fromCharCode(getKeyCode(event));
                                    option.val(value).text(value);
                                }

                                option.prop('selected', true);
                            }
                        }

                        return false;
                    }
                },

                keyUp: function () {
					var opt = options[Combobox.getId($(this))];
                    if (opt.triggerChangeEvent) {
                        $(this).trigger('change');
                    }
                },

                // change event handler
                // handles editable option changing based on a pre-existing values
                change: function () {
                    var opt = options[Combobox.getId($(this))];
                    if (opt.useExistingOptions) {
                        Combobox.setEditableOption($(this));
                    }
                },

                click: function () {
                    if (!$.browser.safari) {
                        Combobox.openedState($(this), !Combobox.openedState($(this)));
                    }
                }
            };
        }());

        // Combobox
        Combobox = (function () {
            var Parameters, EditableOption, generateId, setup;

            // validates and set combobox parameters
            Parameters = (function () {
                var Set, Remove, Handle;

                Set = (function () {
                    var parseKeys, Handles;

                    parseKeys = function (value) {
                        var keys = [];
                        if ($.isArray(value)) {
                            $.each(value, function (i, val) {
                                var j, min, max;
                                if (Validators.keyRange(val)) {
                                    if ($.isArray(val)) {
                                        min = val[0];
                                        max = val[1];
                                    } else {
                                        min = val.min;
                                        max = val.max;
                                    }
                                    for (j = min; j <= max; j += 1) {
                                        keys.push(j);
                                    }
                                } else if (typeof val === 'number' && Validators.integer(val)) {
                                    keys.push(val);
                                }
                            });
                        }
                        return keys;
                    };

                    Handles = (function () {
                        return {
                            integer: function (elem, name, value) {
                                var id = Combobox.getId(elem), opt = options[id];
                                if (opt !== undefined && Validators.integer(value)) {
                                    opt[name] = value;
                                    return true;
                                }
                                return false;
                            },
                            bool: function (elem, name, value) {
                                var id = Combobox.getId(elem), opt = options[id];
                                if (opt !== undefined && typeof value === 'boolean') {
                                    opt[name] = value;
                                    return true;
                                }
                                return false;
                            },
                            array: function (elem, name, value) {
                                if (typeof value === 'string') {
                                    value = [value];
                                }
                                var id = Combobox.getId(elem), opt = options[id];
                                if (opt !== undefined && $.isArray(value)) {
                                    opt[name] = value;
                                    return true;
                                }
                                return false;
                            },
                            object: function (elem, name, value) {
                                var id = Combobox.getId(elem), opt = options[id];
                                if (opt !== undefined && value !== null && $.isPlainObject(value)) {
                                    opt[name] = value;
                                }
                            },
                            keys: function (elem, name, value) {
                                var id = Combobox.getId(elem), opt = options[id];
                                if (opt !== undefined && $.isArray(value)) {
                                    opt[name] = parseKeys(value);
                                }
                            }
                        };
                    }());

                    return {
                        position: function (elem, value) {
                            if (Handles.integer(elem, 'position', value)) {
                                var id = Combobox.getId(elem), opt = options[id], optionsCount;
                                optionsCount =
                                    elem.find('option:not(.' + pluginClass + ')').length;
                                if (value > optionsCount) {
                                    opt.position = optionsCount;
                                }
                            }
                        },

                        ignoreOptGroups: function (elem, value) {
                            Handles.bool(elem, 'ignoreOptGroups', value);
                        },

                        maxLength: function (elem, value) {
                            if (Handles.integer(elem, 'maxLength', value)) {
                                var id = Combobox.getId(elem), opt = options[id];
                                if (value < 0 || value > 255) {
                                    opt.maxLength = 255;
                                }
                            }
                        },

                        classes: function (elem, value) {
                            Handles.array(elem, 'classes', value);
                        },

                        optionClasses: function (elem, value) {
                            Handles.array(elem, 'optionClasses', value);
                        },

                        styles: function (elem, value) {
                            Handles.object(elem, 'styles', value);
                        },

                        optionStyles: function (elem, value) {
                            Handles.object(elem, 'optionStyles', value);
                        },

                        triggerChangeEvent: function (elem, value) {
                            Handles.bool(elem, 'triggerChangeEvent', value);
                        },

                        focusOnNewOption: function (elem, value) {
                            Handles.bool(elem, 'focusOnNewOption', value);
                        },

                        useExistingOptions: function (elem, value) {
                            Handles.bool(elem, 'useExistingOptions', value);
                        },

                        blinkingCursor: function (elem, value) {
                            Handles.bool(elem, 'blinkingCursor', value);
                        },

                        blinkingCursorInterval: function (elem, value) {
                            Handles.integer(elem, 'blinkingCursorInterval', value);
                        },

                        ignoredKeys: function (elem, value) {
                            Handles.keys(elem, 'ignoredKeys', value);
                        },

                        acceptedKeys: function (elem, value) {
                            Handles.keys(elem, 'acceptedKeys', value);
                        }
                    };
                }());

                Remove = (function () {
                    var removeClasses, removeStyles;

                    removeClasses = function (elem, classes) {
                        $.each(classes, function (i, val) {
							elem.removeClass(val);
                        });
                    };

                    removeStyles = function (elem, styles) {
                        $.each(styles, function (key) {
                            elem.css(key, '');
                        });
                    };

                    return {
                        classes: function (elem) {
                            var id = Combobox.getId(elem), opt = options[id];
                            if (opt !== undefined) {
                                removeClasses(elem, opt.classes);
                            }
                        },

                        optionClasses: function (elem) {
                            var id = Combobox.getId(elem), opt = options[id];
                            if (opt !== undefined) {
                                removeClasses(elem.find('option.' + pluginClass),
                                    opt.optionClasses);
                            }
                        },

                        styles: function (elem) {
                            var id = Combobox.getId(elem), opt = options[id];
                            if (opt !== undefined) {
                                removeStyles(elem, opt.styles);
                            }
                        },

                        optionStyles: function (elem) {
                            var id = Combobox.getId(elem), opt = options[id];
                            if (opt !== undefined) {
                                removeStyles(elem.find('option.' + pluginClass),
                                    opt.optionStyles);
                            }
                        },

                        all: function (elem) {
                            Remove.classes(elem);
                            Remove.optionClasses(elem);
                            Remove.styles(elem);
                            Remove.optionStyles(elem);
                        }
                    };
                }());

                Handle = (function () {
                    var setClasses, setStyles;

                    setClasses = function (elem, classes) {
                        $.each(classes, function (i, val) {
                            elem.addClass(String(val));
                        });
                    };

                    setStyles = function (elem, styles) {
                        $.each(styles, function (key, val) {
                            elem.css(key, val);
                        });
                    };

                    return {
                        position: function (elem) {
                            var opt = options[Combobox.getId(elem)], option, uneditableOptions, container;
                            option = elem.find('option.' + pluginClass);

                            uneditableOptions = elem.find('option:not(.' + pluginClass + ')');
                            if (opt.position < uneditableOptions.length) {
                                container = uneditableOptions.eq(opt.position);

                                if (!opt.ignoreOptGroups && container.parent('optgroup').length > 0) {
                                    uneditableOptions.eq(opt.position).parent().before(option);
                                } else {
                                    uneditableOptions.eq(opt.position).before(option);
                                }
                            } else {
                                elem.append(option);
                            }
                        },

                        classes: function (elem) {
                            var id = Combobox.getId(elem), opt = options[id];
                            if (opt !== undefined) {
                                setClasses(elem, opt.classes);
                            }
                        },

                        optionClasses: function (elem) {
                            var id = Combobox.getId(elem), opt = options[id];
                            if (opt !== undefined) {
                                setClasses(elem.find('option.' + pluginClass), opt.optionClasses);
                            }
                        },

                        styles: function (elem) {
                            var id = Combobox.getId(elem), opt = options[id];
                            if (opt !== undefined) {
                                setStyles(elem, opt.styles);
                            }
                        },

                        optionStyles: function (elem) {
                            var id = Combobox.getId(elem), opt = options[id];
                            if (opt !== undefined) {
                                setStyles(elem.find('option.' + pluginClass), opt.optionStyles);
                            }
                        },

                        focusOnNewOption: function (elem) {
                            var id = Combobox.getId(elem), opt = options[id];
                            if (opt !== undefined && opt.focusOnNewOption) {
                                elem.find(':not(option.' + pluginClass + ')').removeProp('selected');
                                elem.find('option.' + pluginClass).prop('selected', true);
                            }
                        },

                        useExistingOptions: function (elem) {
                            var id = Combobox.getId(elem), opt = options[id];
                            if (opt !== undefined && opt.useExistingOptions) {
                                Combobox.setEditableOption(elem);
                            }
                        },

                        all: function (elem) {
                            Handle.position(elem);
                            Handle.classes(elem);
                            Handle.optionClasses(elem);
                            Handle.styles(elem);
                            Handle.optionStyles(elem);
                            Handle.focusOnNewOption(elem);
                            Handle.useExistingOptions(elem);
                        }
                    };
                }());

                return {
                    Set: Set,
                    Remove: Remove,
                    Handle: Handle
                };
            }());

            EditableOption = (function () {
                return {
                    init: function (elem) {
                        if (!elem.find('option.' + pluginClass).length) {
                            var editableOption = $('<option>');
                            editableOption.addClass(pluginClass);
                            elem.append(editableOption);
                        }

                        elem.bind('keydown', EventHandlers.keyDown);
                        elem.bind('keypress', EventHandlers.keyPress);
                        elem.bind('keyup', EventHandlers.keyUp);
                        elem.bind('change', EventHandlers.change);
                        elem.bind('focus', EventHandlers.focus);
                        elem.bind('blur', EventHandlers.blur);
                        elem.bind('click', EventHandlers.click);
                    },

                    destroy: function (elem) {
                        elem.find('option.' + pluginClass).remove();

                        elem.unbind('keydown', EventHandlers.keyDown);
                        elem.unbind('keypress', EventHandlers.keyPress);
                        elem.unbind('keyup', EventHandlers.keyUp);
                        elem.unbind('change', EventHandlers.change);
                        elem.unbind('focus', EventHandlers.focus);
                        elem.unbind('blur', EventHandlers.blur);
                        elem.unbind('click', EventHandlers.click);
                    }
                };
            }());

            // generates unique identifier
            generateId = function () {
                while (true) {
                    var random = Math.floor(Math.random() * 100000);

                    if (options[random] === undefined) {
                        return random;
                    }
                }
            };

            // sets combobox
            setup = function (elem) {
                EditableOption.init(elem);
                Parameters.Handle.all(elem);
            };

            // Combobox public members
            return {
                // create editable combobox
                init: function (settings) {
                    return $(this).filter(':uneditable').each(function () {
                        var id = generateId(), elem = $(this);

                        elem.data('jecId', id);

                        // override passed default options
                        options[id] = $.extend(true, {}, defaults);

                        // parse keys
                        Parameters.Set.ignoredKeys(elem, options[id].ignoredKeys);
                        Parameters.Set.acceptedKeys(elem, options[id].acceptedKeys);

                        if ($.isPlainObject(settings)) {
                            $.each(settings, function (key, val) {
                                if (val !== undefined) {
                                    switch (key) {
                                    case 'position':
                                        Parameters.Set.position(elem, val);
                                        break;
                                    case 'ignoreOptGroups':
                                        Parameters.Set.ignoreOptGroups(elem, val);
                                        break;
                                    case 'maxLength':
                                        Parameters.Set.maxLength(elem, val);
                                        break;
                                    case 'classes':
                                        Parameters.Set.classes(elem, val);
                                        break;
                                    case 'optionClasses':
                                        Parameters.Set.optionClasses(elem, val);
                                        break;
                                    case 'styles':
                                        Parameters.Set.styles(elem, val);
                                        break;
                                    case 'optionStyles':
                                        Parameters.Set.optionStyles(elem, val);
                                        break;
                                    case 'triggerChangeEvent':
                                        Parameters.Set.triggerChangeEvent(elem, val);
                                        break;
                                    case 'focusOnNewOption':
                                        Parameters.Set.focusOnNewOption(elem, val);
                                        break;
                                    case 'useExistingOptions':
                                        Parameters.Set.useExistingOptions(elem, val);
                                        break;
                                    case 'blinkingCursor':
                                        Parameters.Set.blinkingCursor(elem, val);
                                        break;
                                    case 'blinkingCursorInterval':
                                        Parameters.Set.blinkingCursorInterval(elem, val);
                                        break;
                                    case 'ignoredKeys':
                                        Parameters.Set.ignoredKeys(elem, val);
                                        break;
                                    case 'acceptedKeys':
                                        Parameters.Set.acceptedKeys(elem, val);
                                        break;
                                    }
                                }
                            });
                        }

                        setup($(this));
                    });
                },

                // creates editable combobox without using existing select elements
                initJS: function (options, settings) {
                    var select, addOptions;

                    select = $('<select>');

                    addOptions = function (elem, options) {
                        if ($.isArray(options)) {
                            $.each(options, function (i, val) {
                                if ($.isPlainObject(val)) {
                                    $.each(val, function (key, value) {
                                        if ($.isArray(value)) {
                                            var og = $('<optgroup>').attr('label', key);
                                            addOptions(og, value);
                                            og.appendTo(select);
                                        } else if (typeof value === 'number' || typeof value === 'string') {
                                            $('<option>').text(value).attr('value', key)
                                                .appendTo(elem);
                                        }
                                    });
                                } else if (typeof val === 'string' || typeof val === 'number') {
                                    $('<option>').text(val).attr('value', val).appendTo(elem);
                                }
                            });
                        }
                    };

                    addOptions(select, options);

                    return select.jec(settings);
                },

                // destroys editable combobox
                destroy: function () {
                    return $(this).filter(':editable').each(function () {
                        $(this).jecOff();
                        $.removeData($(this).get(0), 'jecId');
                        $.removeData($(this).get(0), 'jecCursorState');
                        $.removeData($(this).get(0), 'jecOpenedState');
                    });
                },

                // enable editablecombobox
                enable: function () {
                    return $(this).filter(':editable').each(function () {
                        var id = Combobox.getId($(this)), value = values[id];

                        setup($(this));

                        if (value !== undefined) {
                            $(this).jecValue(value);
                        }
                    });
                },

                // disable editable combobox
                disable: function () {
                    return $(this).filter(':editable').each(function () {
                        var val = $(this).find('option.' + pluginClass).val();
                        values[Combobox.getId($(this))] = val;
                        Parameters.Remove.all($(this));
                        EditableOption.destroy($(this));
                    });
                },

                // gets or sets editable option's value
                value: function (value, setFocus) {
                    if ($(this).filter(':editable').length > 0) {
                        if (value === null || value === undefined) {
                            // get value
                            return $(this).find('option.' + pluginClass).val();
                        } else if (typeof value === 'string' || typeof value === 'number') {
                            // set value
                            return $(this).filter(':editable').each(function () {
                                var option = $(this).find('option.' + pluginClass);
                                option.val(value).text(value);
                                if (typeof setFocus !== 'boolean' || setFocus) {
                                    option.prop('selected', true);
                                }
                            });
                        }
                    }
                },

                // gets or sets editable option's preference
                pref: function (name, value) {
                    if ($(this).filter(':editable').length > 0) {
                        if (typeof name === 'string') {
                            if (value === null || value === undefined) {
                                // get preference
                                return options[Combobox.getId($(this))][name];
                            } else {
                                // set preference
                                return $(this).filter(':editable').each(function () {
                                    switch (name) {
                                    case 'position':
                                        Parameters.Set.position($(this), value);
                                        Parameters.Handle.position($(this));
                                        break;
                                    case 'classes':
                                        Parameters.Remove.classes($(this));
                                        Parameters.Set.classes($(this), value);
                                        Parameters.Handle.position($(this));
                                        break;
                                    case 'optionClasses':
                                        Parameters.Remove.optionClasses($(this));
                                        Parameters.Set.optionClasses($(this), value);
                                        Parameters.Set.optionClasses($(this));
                                        break;
                                    case 'styles':
                                        Parameters.Remove.styles($(this));
                                        Parameters.Set.styles($(this), value);
                                        Parameters.Set.styles($(this));
                                        break;
                                    case 'optionStyles':
                                        Parameters.Remove.optionStyles($(this));
                                        Parameters.Set.optionStyles($(this), value);
                                        Parameters.Handle.optionStyles($(this));
                                        break;
                                    case 'focusOnNewOption':
                                        Parameters.Set.focusOnNewOption($(this), value);
                                        Parameters.Handle.focusOnNewOption($(this));
                                        break;
                                    case 'useExistingOptions':
                                        Parameters.Set.useExistingOptions($(this), value);
                                        Parameters.Handle.useExistingOptions($(this));
                                        break;
                                    case 'blinkingCursor':
                                        Parameters.Set.blinkingCursor($(this), value);
                                        break;
                                    case 'blinkingCursorInterval':
                                        Parameters.Set.blinkingCursorInterval($(this), value);
                                        break;
                                    case 'ignoredKeys':
                                        Parameters.Set.ignoredKeys($(this), value);
                                        break;
                                    case 'acceptedKeys':
                                        Parameters.Set.acceptedKeys($(this), value);
                                        break;
                                    }
                                });
                            }
                        }
                    }
                },

                // sets editable option to the value of currently selected option
                setEditableOption: function (elem) {
                    var value = elem.find('option:selected').text();
                    elem.find('option.' + pluginClass).attr('value', elem.val()).text(value).prop('selected', true);
                },

                // get combobox id
                getId: function (elem) {
                    return elem.data('jecId');
                },

                valueIsEditable: function (elem) {
                    return elem.find('option.' + pluginClass).get(0) === elem.find('option:selected').get(0);
                },

                clearCursor: function (elem) {
                    $(elem).find('option.' + cursorClass).each(function () {
                        var text = $(this).text();
                        $(this).removeClass(cursorClass).text(text.substring(0, text.length - 1));
                    });
                },

                cursorState: function (elem, state) {
                    return elem.data('jecCursorState', state);
                },

                focusedState: function (elem, state) {
               		return $(elem).data('jecFocusedState', state);
                },

                openedState: function (elem, state) {
                    return elem.data('jecOpenedState', state);
                },

                //handles editable cursor
                handleCursor: function () {
                    if (activeCombobox !== undefined && activeCombobox !== null) {
                        if ($.browser.msie && Combobox.openedState(activeCombobox)) {
                            return;
                        }

                        var state = Combobox.cursorState(activeCombobox), elem;
                        if (state) {
                            Combobox.clearCursor(activeCombobox);
                        } else if (Combobox.valueIsEditable(activeCombobox)) {
                            elem = activeCombobox.find('option:selected');
                            elem.addClass(cursorClass).text(elem.text() + '|');
                        }
                        Combobox.cursorState(activeCombobox, !state);
                    }
                }
            };
        }());

        // jEC public members
        return {
            init: Combobox.init,
            enable: Combobox.enable,
            disable: Combobox.disable,
            destroy: Combobox.destroy,
            value: Combobox.value,
            pref: Combobox.pref,
            initJS: Combobox.initJS,
            handleCursor: Combobox.handleCursor
        };
    }());

    // register functions
    $.fn.extend({
        jec: $.jEC.init,
        jecOn: $.jEC.enable,
        jecOff: $.jEC.disable,
        jecKill: $.jEC.destroy,
        jecValue: $.jEC.value,
        jecPref: $.jEC.pref
    });

    $.extend({
        jec: $.jEC.initJS
    });

    // register selectors
    $.extend($.expr[':'], {
        editable: function (a) {
            var data = $(a).data('jecId');
            return data !== null && data !== undefined;
        },

        uneditable: function (a) {
            var data = $(a).data('jecId');
            return data === null || data === undefined;
        }
    });

}(jQuery));

// mredkj.com
// created: 2001-07-11
// last updated: 2001-09-07
// Description: Manage two combinate lists

var NS4 = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) < 5);

function addOption(theSel, theText, theValue)
{
	var newOpt = new Option(theText, theValue);
	var selLength = theSel.length;
	theSel.options[selLength] = newOpt;
}

function deleteOption(theSel, theIndex)
{	
	var selLength = theSel.length;
	if(selLength>0)
	{
		theSel.options[theIndex] = null;
	}
}

function moveOptions(theSelFrom, theSelTo)
{
	var selLength = theSelFrom.length;
	var selectedText = new Array();
	var selectedValues = new Array();
	var selectedCount = 0;
	
	var i;
	
	// Find the selected Options in reverse order
	// and delete them from the 'from' Select.
	for(i=selLength-1; i>=0; i--)
	{
		if(theSelFrom.options[i].selected)
		{
			selectedText[selectedCount] = theSelFrom.options[i].text;
			selectedValues[selectedCount] = theSelFrom.options[i].value;
			deleteOption(theSelFrom, i);
			selectedCount++;
		}
	}
	
	// Add the selected text/values in reverse order.
	// This will add the Options to the 'to' Select
	// in the same order as they were in the 'from' Select.
	for(i=selectedCount-1; i>=0; i--)
	{
		addOption(theSelTo, selectedText[i], selectedValues[i]);
	}
	
	if(NS4) history.go(0);
}

/*
Sort <SELECT> field script by Babvailiica
www.babailiica.com
version 1.3
*/

function selectall(obj) {
	obj = (typeof obj == "string") ? document.getElementById(obj) : obj;
	if (obj.tagName.toLowerCase() != "select")
		return;
	for (var i=0; i<obj.length; i++) {
		obj[i].selected = true;
	}
}

function selectnone(obj) { /* NEW added from version 1.1 */
	obj = (typeof obj == "string") ? document.getElementById(obj) : obj;
	if (obj.tagName.toLowerCase() != "select")
		return;
	for (var i=0; i<obj.length; i++) {
		obj[i].selected = false;
	}
}

function swap(obj) { /*updated from version 1.3*/
	obj = (typeof obj == "string") ? document.getElementById(obj) : obj;
	if (obj.tagName.toLowerCase() != "select" && obj.length < 2)
		return false;
	var first_element = false;
	var last_element = false;
	for (var i=0; i<obj.length; i++) {
		if (obj[i].selected) {
			if (first_element === false) {
				first_element = i;
			} else {
				last_element = i;
			}
		}
	}

	if (first_element === false || last_element === false)
		return false;

	var tmp = new Array((document.body.innerHTML ? obj[first_element].innerHTML : obj[first_element].text), obj[first_element].value, obj[first_element].style.color, obj[first_element].style.backgroundColor, obj[first_element].className, obj[first_element].id, obj[first_element].selected);
	if (document.body.innerHTML) obj[first_element].innerHTML = obj[last_element].innerHTML;
	else obj[first_element].text = obj[last_element].text;
	obj[first_element].value = obj[last_element].value;
	obj[first_element].style.color = obj[last_element].style.color;
	obj[first_element].style.backgroundColor = obj[last_element].style.backgroundColor;
	obj[first_element].className = obj[last_element].className;
	obj[first_element].id = obj[last_element].id;
	obj[first_element].selected = obj[last_element].selected;
	if (document.body.innerHTML) obj[last_element].innerHTML = tmp[0];
	else obj[last_element].text = tmp[0];
	obj[last_element].value = tmp[1];
	obj[last_element].style.color = tmp[2];
	obj[last_element].style.backgroundColor = tmp[3];
	obj[last_element].className = tmp[4];
	obj[last_element].id = tmp[5];
	obj[last_element].selected = tmp[6];
}

function additem(obj, text, value, index, id, classname, color, bg, selected) { /* NEW added from version 1.1 updated from version 1.2*/
	obj = (typeof obj == "string") ? document.getElementById(obj) : obj;
	if (obj.tagName.toLowerCase() != "select" || text == "")
		return;
	obj.length++;
	if (typeof index == "number" && index < obj.length-1) {
		var i = Number();
		for (i=obj.length-2; i>index-1; i--) {
			if (document.body.innerHTML) obj[i+1].innerHTML = obj[i].innerHTML;
			else obj[i+1].text = obj[i].text;
			obj[i+1].value = obj[i].value;
			obj[i+1].id = obj[i].id;
			obj[i+1].className = obj[i].className;
			obj[i+1].style.color = obj[i].style.color;
			obj[i+1].style.backgroundColor = obj[i].style.backgroundColor;
			obj[i+1].selected = obj[i].selected;
		}
	} else {
		index = obj.length - 1;
	}
	obj = obj[index];
	if (document.body.innerHTML) obj.innerHTML = text;
	else obj.text = text;
	obj.value = value;
	obj.id = id ? id : '';
	obj.className = classname ? classname : '';
	obj.style.color = color ? color : '';
	obj.style.backgroundColor = bg ? bg : '';
	obj.selected = selected
}

function removeitem(obj, index) { /* NEW added from version 1.1 */
	obj = (typeof obj == "string") ? document.getElementById(obj) : obj;
	if (obj.tagName.toLowerCase() != "select" || obj.length == 0)
		return;
	if (index === true) {
		for (index=obj.length-1; index>=0; index--) {
			if (obj[index].selected) {
				obj[index] = null;
			}
		}
	} else {
		obj[((typeof index != "number") || index > (obj.length - 1) || index < 0 ? obj.length - 1 : index)] = null;
	}
}

function mousewheel(obj) {
	obj = (typeof obj == "string") ? document.getElementById(obj) : obj;
	if (obj.tagName.toLowerCase() != "select")
		return;
	if (obj.selectedIndex != -1) {
		if (event.wheelDelta > 0) {
			upone(obj);
		} else {
			downone(obj);
		}
		return false;
	}
}

function sort2d(arrayName, element, num, cs) {
	if (num) {
		for (var i=0; i<(arrayName.length-1); i++) {
			for (var j=i+1; j<arrayName.length; j++) {
				if (parseInt(arrayName[j][element],10) < parseInt(arrayName[i][element],10)) {
					var dummy = arrayName[i];
					arrayName[i] = arrayName[j];
					arrayName[j] = dummy;
				}
			}
		}
	} else {
		for (var i=0; i<(arrayName.length-1); i++) {
			for (var j=i+1; j<arrayName.length; j++) {
				if (cs) {
					if (arrayName[j][element].toLowerCase() < arrayName[i][element].toLowerCase()) {
						var dummy = arrayName[i];
						arrayName[i] = arrayName[j];
						arrayName[j] = dummy;
					}
				} else {
					if (arrayName[j][element] < arrayName[i][element]) {
						var dummy = arrayName[i];
						arrayName[i] = arrayName[j];
						arrayName[j] = dummy;
					}
				}
			}
		}
	}
}

/* sort the list!
by = 0 - order by text (default)
by = 1 - order by value
by = 2 - order by color
by = 3 - order by background color
by = 4 - order by class name
by = 5 - order by id
num = if true sorts numbers e.g. 2 before 10
cs = casesensitive e.g. a before Z*/
function listsort(obj, by, num, cs) { /*updated from version 1.2*/
	obj = (typeof obj == "string") ? document.getElementById(obj) : obj;
	by = (parseInt("0" + by) > 5) ? 0 : parseInt("0" + by);
	if (obj.tagName.toLowerCase() != "select" && obj.length < 2)
		return false;
	var elements = new Array();
	for (var i=0; i<obj.length; i++) {
		elements[elements.length] = new Array((document.body.innerHTML ? obj[i].innerHTML : obj[i].text), obj[i].value, (obj[i].currentStyle ? obj[i].currentStyle.color : obj[i].style.color), (obj[i].currentStyle ? obj[i].currentStyle.backgroundColor : obj[i].style.backgroundColor), obj[i].className, obj[i].id, obj[i].selected);
	}
	sort2d(elements, by, num, cs);
	for (i=0; i<obj.length; i++) {
		if (document.body.innerHTML) obj[i].innerHTML = elements[i][0];
		else obj[i].text = elements[i][0];
		obj[i].value = elements[i][1];
		obj[i].style.color = elements[i][2];
		obj[i].style.backgroundColor = elements[i][3];
		obj[i].className = elements[i][4];
		obj[i].id = elements[i][5];
		obj[i].selected = elements[i][6];
	}
}

function viceversa(obj, onlyselected) { /*updated from version 1.3*/
	obj = (typeof obj == "string") ? document.getElementById(obj) : obj;
	if (obj.tagName.toLowerCase() != "select" && obj.length < 2)
		return false;
	var elements = new Array();
	for (var i=obj.length-1; i>-1; i--) {
		if (obj[i].selected || !onlyselected) {
			elements[elements.length] = new Array((document.body.innerHTML ? obj[i].innerHTML : obj[i].text), obj[i].value, obj[i].style.color, obj[i].style.backgroundColor, obj[i].className, obj[i].id, obj[i].selected);
		}
	}
	var a = 0;
	for (i=0; i<obj.length; i++) {
		if (obj[i].selected || !onlyselected) {
			if (document.body.innerHTML) obj[i].innerHTML = elements[a][0];
			else obj[i].text = elements[a][0];
			obj[i].value = elements[a][1];
			obj[i].style.color = elements[a][2];
			obj[i].style.backgroundColor = elements[a][3];
			obj[i].className = elements[a][4];
			obj[i].id = elements[a][5];
			obj[i].selected = elements[a][6];
			a++;
		}
	}
}

function list_top(obj) { /*updated from version 1.2*/
	obj = (typeof obj == "string") ? document.getElementById(obj) : obj;
	if (obj.tagName.toLowerCase() != "select" && obj.length < 2)
		return false;
	var elements = new Array();
	for (var i=0; i<obj.length; i++) {
		if (obj[i].selected) {
			elements[elements.length] = new Array((document.body.innerHTML ? obj[i].innerHTML : obj[i].text), obj[i].value, obj[i].style.color, obj[i].style.backgroundColor, obj[i].className, obj[i].id, obj[i].selected);
		}
	}
	for (i=0; i<obj.length; i++) {
		if (!obj[i].selected) {
			elements[elements.length] = new Array((document.body.innerHTML ? obj[i].innerHTML : obj[i].text), obj[i].value, obj[i].style.color, obj[i].style.backgroundColor, obj[i].className, obj[i].id, obj[i].selected);
		}
	}
	for (i=0; i<obj.length; i++) {
		if (document.body.innerHTML) obj[i].innerHTML = elements[i][0];
		else obj[i].text = elements[i][0];
		obj[i].value = elements[i][1];
		obj[i].style.color = elements[i][2];
		obj[i].style.backgroundColor = elements[i][3];
		obj[i].className = elements[i][4];
		obj[i].id = elements[i][5];
		obj[i].selected = elements[i][6];
	}
}

function bottom(obj) { /*updated from version 1.2*/
	obj = (typeof obj == "string") ? document.getElementById(obj) : obj;
	if (obj.tagName.toLowerCase() != "select" && obj.length < 2)
		return false;
	var elements = new Array();
	for (var i=0; i<obj.length; i++) {
		if (!obj[i].selected) {
			elements[elements.length] = new Array((document.body.innerHTML ? obj[i].innerHTML : obj[i].text), obj[i].value, obj[i].style.color, obj[i].style.backgroundColor, obj[i].className, obj[i].id, obj[i].selected);
		}
	}
	for (i=0; i<obj.length; i++) {
		if (obj[i].selected) {
			elements[elements.length] = new Array((document.body.innerHTML ? obj[i].innerHTML : obj[i].text), obj[i].value, obj[i].style.color, obj[i].style.backgroundColor, obj[i].className, obj[i].id, obj[i].selected);
		}
	}
	for (i=obj.length-1; i>-1; i--) {
		if (document.body.innerHTML) obj[i].innerHTML = elements[i][0];
		else obj[i].text = elements[i][0];
		obj[i].value = elements[i][1];
		obj[i].style.color = elements[i][2];
		obj[i].style.backgroundColor = elements[i][3];
		obj[i].className = elements[i][4];
		obj[i].id = elements[i][5];
		obj[i].selected = elements[i][6];
	}
}

function upone(obj) { /*updated from version 1.2*/
	obj = (typeof obj == "string") ? document.getElementById(obj) : obj;
	if (obj.tagName.toLowerCase() != "select" && obj.length < 2)
		return false;
	var sel = new Array();
	for (var i=0; i<obj.length; i++) {
		if (obj[i].selected == true) {
			sel[sel.length] = i;
		}
	}
	for (i in sel) {
		if (sel[i] != 0 && !obj[sel[i]-1].selected) {
			var tmp = new Array((document.body.innerHTML ? obj[sel[i]-1].innerHTML : obj[sel[i]-1].text), obj[sel[i]-1].value, obj[sel[i]-1].style.color, obj[sel[i]-1].style.backgroundColor, obj[sel[i]-1].className, obj[sel[i]-1].id);
			if (document.body.innerHTML) obj[sel[i]-1].innerHTML = obj[sel[i]].innerHTML;
			else obj[sel[i]-1].text = obj[sel[i]].text;
			obj[sel[i]-1].value = obj[sel[i]].value;
			obj[sel[i]-1].style.color = obj[sel[i]].style.color;
			obj[sel[i]-1].style.backgroundColor = obj[sel[i]].style.backgroundColor;
			obj[sel[i]-1].className = obj[sel[i]].className;
			obj[sel[i]-1].id = obj[sel[i]].id;
			if (document.body.innerHTML) obj[sel[i]].innerHTML = tmp[0];
			else obj[sel[i]].text = tmp[0];
			obj[sel[i]].value = tmp[1];
			obj[sel[i]].style.color = tmp[2];
			obj[sel[i]].style.backgroundColor = tmp[3];
			obj[sel[i]].className = tmp[4];
			obj[sel[i]].id = tmp[5];
			obj[sel[i]-1].selected = true;
			obj[sel[i]].selected = false;
		}
	}
}

function downone(obj) {
	obj = (typeof obj == "string") ? document.getElementById(obj) : obj;
	if (obj.tagName.toLowerCase() != "select" && obj.length < 2)
		return false;
	var sel = new Array();
	for (var i=obj.length-1; i>-1; i--) {
		if (obj[i].selected == true) {
			sel[sel.length] = i;
		}
	}
	for (i in sel) {
		if (sel[i] != obj.length-1 && !obj[sel[i]+1].selected) {
			var tmp = new Array((document.body.innerHTML ? obj[sel[i]+1].innerHTML : obj[sel[i]+1].text), obj[sel[i]+1].value, obj[sel[i]+1].style.color, obj[sel[i]+1].style.backgroundColor, obj[sel[i]+1].className, obj[sel[i]+1].id);
			if (document.body.innerHTML) obj[sel[i]+1].innerHTML = obj[sel[i]].innerHTML;
			else obj[sel[i]+1].text = obj[sel[i]].text;
			obj[sel[i]+1].value = obj[sel[i]].value;
			obj[sel[i]+1].style.color = obj[sel[i]].style.color;
			obj[sel[i]+1].style.backgroundColor = obj[sel[i]].style.backgroundColor;
			obj[sel[i]+1].className = obj[sel[i]].className;
			obj[sel[i]+1].id = obj[sel[i]].id;
			if (document.body.innerHTML) obj[sel[i]].innerHTML = tmp[0];
			else obj[sel[i]].text = tmp[0];
			obj[sel[i]].value = tmp[1];
			obj[sel[i]].style.color = tmp[2];
			obj[sel[i]].style.backgroundColor = tmp[3];
			obj[sel[i]].className = tmp[4];
			obj[sel[i]].id = tmp[5];
			obj[sel[i]+1].selected = true;
			obj[sel[i]].selected = false;
		}
	}
}

function inarray(v,a) {
	for (var i in a) {
		if (a[i] == v) {
			return true;
		}
	}
	return false;
}
/*
 * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
 * Digest Algorithm, as defined in RFC 1321.
 * Version 2.2 Copyright (C) Paul Johnston 1999 - 2009
 * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
 * Distributed under the BSD License
 * See http://pajhome.org.uk/crypt/md5 for more info.
 */

/*
 * Configurable variables. You may need to tweak these to be compatible with
 * the server-side, but the defaults work in most cases.
 */
var hexcase = 0;   /* hex output format. 0 - lowercase; 1 - uppercase        */
var b64pad  = "";  /* base-64 pad character. "=" for strict RFC compliance   */

/*
 * These are the functions you'll usually want to call
 * They take string arguments and return either hex or base-64 encoded strings
 */
function hex_md5(s)    { return rstr2hex(rstr_md5(str2rstr_utf8(s))); }
function b64_md5(s)    { return rstr2b64(rstr_md5(str2rstr_utf8(s))); }
function any_md5(s, e) { return rstr2any(rstr_md5(str2rstr_utf8(s)), e); }
function hex_hmac_md5(k, d)
  { return rstr2hex(rstr_hmac_md5(str2rstr_utf8(k), str2rstr_utf8(d))); }
function b64_hmac_md5(k, d)
  { return rstr2b64(rstr_hmac_md5(str2rstr_utf8(k), str2rstr_utf8(d))); }
function any_hmac_md5(k, d, e)
  { return rstr2any(rstr_hmac_md5(str2rstr_utf8(k), str2rstr_utf8(d)), e); }

/*
 * Perform a simple self-test to see if the VM is working
 */
function md5_vm_test()
{
  return hex_md5("abc").toLowerCase() == "900150983cd24fb0d6963f7d28e17f72";
}

/*
 * Calculate the MD5 of a raw string
 */
function rstr_md5(s)
{
  return binl2rstr(binl_md5(rstr2binl(s), s.length * 8));
}

/*
 * Calculate the HMAC-MD5, of a key and some data (raw strings)
 */
function rstr_hmac_md5(key, data)
{
  var bkey = rstr2binl(key);
  if(bkey.length > 16) bkey = binl_md5(bkey, key.length * 8);

  var ipad = Array(16), opad = Array(16);
  for(var i = 0; i < 16; i++)
  {
    ipad[i] = bkey[i] ^ 0x36363636;
    opad[i] = bkey[i] ^ 0x5C5C5C5C;
  }

  var hash = binl_md5(ipad.concat(rstr2binl(data)), 512 + data.length * 8);
  return binl2rstr(binl_md5(opad.concat(hash), 512 + 128));
}

/*
 * Convert a raw string to a hex string
 */
function rstr2hex(input)
{
  try { hexcase } catch(e) { hexcase=0; }
  var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
  var output = "";
  var x;
  for(var i = 0; i < input.length; i++)
  {
    x = input.charCodeAt(i);
    output += hex_tab.charAt((x >>> 4) & 0x0F)
           +  hex_tab.charAt( x        & 0x0F);
  }
  return output;
}

/*
 * Convert a raw string to a base-64 string
 */
function rstr2b64(input)
{
  try { b64pad } catch(e) { b64pad=''; }
  var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
  var output = "";
  var len = input.length;
  for(var i = 0; i < len; i += 3)
  {
    var triplet = (input.charCodeAt(i) << 16)
                | (i + 1 < len ? input.charCodeAt(i+1) << 8 : 0)
                | (i + 2 < len ? input.charCodeAt(i+2)      : 0);
    for(var j = 0; j < 4; j++)
    {
      if(i * 8 + j * 6 > input.length * 8) output += b64pad;
      else output += tab.charAt((triplet >>> 6*(3-j)) & 0x3F);
    }
  }
  return output;
}

/*
 * Convert a raw string to an arbitrary string encoding
 */
function rstr2any(input, encoding)
{
  var divisor = encoding.length;
  var i, j, q, x, quotient;

  /* Convert to an array of 16-bit big-endian values, forming the dividend */
  var dividend = Array(Math.ceil(input.length / 2));
  for(i = 0; i < dividend.length; i++)
  {
    dividend[i] = (input.charCodeAt(i * 2) << 8) | input.charCodeAt(i * 2 + 1);
  }

  /*
   * Repeatedly perform a long division. The binary array forms the dividend,
   * the length of the encoding is the divisor. Once computed, the quotient
   * forms the dividend for the next step. All remainders are stored for later
   * use.
   */
  var full_length = Math.ceil(input.length * 8 /
                                    (Math.log(encoding.length) / Math.log(2)));
  var remainders = Array(full_length);
  for(j = 0; j < full_length; j++)
  {
    quotient = Array();
    x = 0;
    for(i = 0; i < dividend.length; i++)
    {
      x = (x << 16) + dividend[i];
      q = Math.floor(x / divisor);
      x -= q * divisor;
      if(quotient.length > 0 || q > 0)
        quotient[quotient.length] = q;
    }
    remainders[j] = x;
    dividend = quotient;
  }

  /* Convert the remainders to the output string */
  var output = "";
  for(i = remainders.length - 1; i >= 0; i--)
    output += encoding.charAt(remainders[i]);

  return output;
}

/*
 * Encode a string as utf-8.
 * For efficiency, this assumes the input is valid utf-16.
 */
function str2rstr_utf8(input)
{
  var output = "";
  var i = -1;
  var x, y;

  while(++i < input.length)
  {
    /* Decode utf-16 surrogate pairs */
    x = input.charCodeAt(i);
    y = i + 1 < input.length ? input.charCodeAt(i + 1) : 0;
    if(0xD800 <= x && x <= 0xDBFF && 0xDC00 <= y && y <= 0xDFFF)
    {
      x = 0x10000 + ((x & 0x03FF) << 10) + (y & 0x03FF);
      i++;
    }

    /* Encode output as utf-8 */
    if(x <= 0x7F)
      output += String.fromCharCode(x);
    else if(x <= 0x7FF)
      output += String.fromCharCode(0xC0 | ((x >>> 6 ) & 0x1F),
                                    0x80 | ( x         & 0x3F));
    else if(x <= 0xFFFF)
      output += String.fromCharCode(0xE0 | ((x >>> 12) & 0x0F),
                                    0x80 | ((x >>> 6 ) & 0x3F),
                                    0x80 | ( x         & 0x3F));
    else if(x <= 0x1FFFFF)
      output += String.fromCharCode(0xF0 | ((x >>> 18) & 0x07),
                                    0x80 | ((x >>> 12) & 0x3F),
                                    0x80 | ((x >>> 6 ) & 0x3F),
                                    0x80 | ( x         & 0x3F));
  }
  return output;
}

/*
 * Encode a string as utf-16
 */
function str2rstr_utf16le(input)
{
  var output = "";
  for(var i = 0; i < input.length; i++)
    output += String.fromCharCode( input.charCodeAt(i)        & 0xFF,
                                  (input.charCodeAt(i) >>> 8) & 0xFF);
  return output;
}

function str2rstr_utf16be(input)
{
  var output = "";
  for(var i = 0; i < input.length; i++)
    output += String.fromCharCode((input.charCodeAt(i) >>> 8) & 0xFF,
                                   input.charCodeAt(i)        & 0xFF);
  return output;
}

/*
 * Convert a raw string to an array of little-endian words
 * Characters >255 have their high-byte silently ignored.
 */
function rstr2binl(input)
{
  var output = Array(input.length >> 2);
  for(var i = 0; i < output.length; i++)
    output[i] = 0;
  for(var i = 0; i < input.length * 8; i += 8)
    output[i>>5] |= (input.charCodeAt(i / 8) & 0xFF) << (i%32);
  return output;
}

/*
 * Convert an array of little-endian words to a string
 */
function binl2rstr(input)
{
  var output = "";
  for(var i = 0; i < input.length * 32; i += 8)
    output += String.fromCharCode((input[i>>5] >>> (i % 32)) & 0xFF);
  return output;
}

/*
 * Calculate the MD5 of an array of little-endian words, and a bit length.
 */
function binl_md5(x, len)
{
  /* append padding */
  x[len >> 5] |= 0x80 << ((len) % 32);
  x[(((len + 64) >>> 9) << 4) + 14] = len;

  var a =  1732584193;
  var b = -271733879;
  var c = -1732584194;
  var d =  271733878;

  for(var i = 0; i < x.length; i += 16)
  {
    var olda = a;
    var oldb = b;
    var oldc = c;
    var oldd = d;

    a = md5_ff(a, b, c, d, x[i+ 0], 7 , -680876936);
    d = md5_ff(d, a, b, c, x[i+ 1], 12, -389564586);
    c = md5_ff(c, d, a, b, x[i+ 2], 17,  606105819);
    b = md5_ff(b, c, d, a, x[i+ 3], 22, -1044525330);
    a = md5_ff(a, b, c, d, x[i+ 4], 7 , -176418897);
    d = md5_ff(d, a, b, c, x[i+ 5], 12,  1200080426);
    c = md5_ff(c, d, a, b, x[i+ 6], 17, -1473231341);
    b = md5_ff(b, c, d, a, x[i+ 7], 22, -45705983);
    a = md5_ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
    d = md5_ff(d, a, b, c, x[i+ 9], 12, -1958414417);
    c = md5_ff(c, d, a, b, x[i+10], 17, -42063);
    b = md5_ff(b, c, d, a, x[i+11], 22, -1990404162);
    a = md5_ff(a, b, c, d, x[i+12], 7 ,  1804603682);
    d = md5_ff(d, a, b, c, x[i+13], 12, -40341101);
    c = md5_ff(c, d, a, b, x[i+14], 17, -1502002290);
    b = md5_ff(b, c, d, a, x[i+15], 22,  1236535329);

    a = md5_gg(a, b, c, d, x[i+ 1], 5 , -165796510);
    d = md5_gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
    c = md5_gg(c, d, a, b, x[i+11], 14,  643717713);
    b = md5_gg(b, c, d, a, x[i+ 0], 20, -373897302);
    a = md5_gg(a, b, c, d, x[i+ 5], 5 , -701558691);
    d = md5_gg(d, a, b, c, x[i+10], 9 ,  38016083);
    c = md5_gg(c, d, a, b, x[i+15], 14, -660478335);
    b = md5_gg(b, c, d, a, x[i+ 4], 20, -405537848);
    a = md5_gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
    d = md5_gg(d, a, b, c, x[i+14], 9 , -1019803690);
    c = md5_gg(c, d, a, b, x[i+ 3], 14, -187363961);
    b = md5_gg(b, c, d, a, x[i+ 8], 20,  1163531501);
    a = md5_gg(a, b, c, d, x[i+13], 5 , -1444681467);
    d = md5_gg(d, a, b, c, x[i+ 2], 9 , -51403784);
    c = md5_gg(c, d, a, b, x[i+ 7], 14,  1735328473);
    b = md5_gg(b, c, d, a, x[i+12], 20, -1926607734);

    a = md5_hh(a, b, c, d, x[i+ 5], 4 , -378558);
    d = md5_hh(d, a, b, c, x[i+ 8], 11, -2022574463);
    c = md5_hh(c, d, a, b, x[i+11], 16,  1839030562);
    b = md5_hh(b, c, d, a, x[i+14], 23, -35309556);
    a = md5_hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
    d = md5_hh(d, a, b, c, x[i+ 4], 11,  1272893353);
    c = md5_hh(c, d, a, b, x[i+ 7], 16, -155497632);
    b = md5_hh(b, c, d, a, x[i+10], 23, -1094730640);
    a = md5_hh(a, b, c, d, x[i+13], 4 ,  681279174);
    d = md5_hh(d, a, b, c, x[i+ 0], 11, -358537222);
    c = md5_hh(c, d, a, b, x[i+ 3], 16, -722521979);
    b = md5_hh(b, c, d, a, x[i+ 6], 23,  76029189);
    a = md5_hh(a, b, c, d, x[i+ 9], 4 , -640364487);
    d = md5_hh(d, a, b, c, x[i+12], 11, -421815835);
    c = md5_hh(c, d, a, b, x[i+15], 16,  530742520);
    b = md5_hh(b, c, d, a, x[i+ 2], 23, -995338651);

    a = md5_ii(a, b, c, d, x[i+ 0], 6 , -198630844);
    d = md5_ii(d, a, b, c, x[i+ 7], 10,  1126891415);
    c = md5_ii(c, d, a, b, x[i+14], 15, -1416354905);
    b = md5_ii(b, c, d, a, x[i+ 5], 21, -57434055);
    a = md5_ii(a, b, c, d, x[i+12], 6 ,  1700485571);
    d = md5_ii(d, a, b, c, x[i+ 3], 10, -1894986606);
    c = md5_ii(c, d, a, b, x[i+10], 15, -1051523);
    b = md5_ii(b, c, d, a, x[i+ 1], 21, -2054922799);
    a = md5_ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
    d = md5_ii(d, a, b, c, x[i+15], 10, -30611744);
    c = md5_ii(c, d, a, b, x[i+ 6], 15, -1560198380);
    b = md5_ii(b, c, d, a, x[i+13], 21,  1309151649);
    a = md5_ii(a, b, c, d, x[i+ 4], 6 , -145523070);
    d = md5_ii(d, a, b, c, x[i+11], 10, -1120210379);
    c = md5_ii(c, d, a, b, x[i+ 2], 15,  718787259);
    b = md5_ii(b, c, d, a, x[i+ 9], 21, -343485551);

    a = safe_add(a, olda);
    b = safe_add(b, oldb);
    c = safe_add(c, oldc);
    d = safe_add(d, oldd);
  }
  return Array(a, b, c, d);
}

/*
 * These functions implement the four basic operations the algorithm uses.
 */
function md5_cmn(q, a, b, x, s, t)
{
  return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s),b);
}
function md5_ff(a, b, c, d, x, s, t)
{
  return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
}
function md5_gg(a, b, c, d, x, s, t)
{
  return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
}
function md5_hh(a, b, c, d, x, s, t)
{
  return md5_cmn(b ^ c ^ d, a, b, x, s, t);
}
function md5_ii(a, b, c, d, x, s, t)
{
  return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
}

/*
 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
 * to work around bugs in some JS interpreters.
 */
function safe_add(x, y)
{
  var lsw = (x & 0xFFFF) + (y & 0xFFFF);
  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
  return (msw << 16) | (lsw & 0xFFFF);
}

/*
 * Bitwise rotate a 32-bit number to the left.
 */
function bit_rol(num, cnt)
{
  return (num << cnt) | (num >>> (32 - cnt));
}
function error_handler(a,b,c)
{
 window.status = (a +"\n" + b + "\n\n" + c + "\n\n" + error_handler.caller);
 return true;
}
window.onerror = error_handler;
String.prototype.trim=function(){return this.replace(/(^\s+)|\s+$/g,"");}


self.WD = {
/*begin of functions==============================*/
browser:new Object(),
dull:function (){},


addEvent:function(obj, evType, fn)
{ 
	if (obj.addEventListener)
	{  
		obj.addEventListener(evType, fn, false);  return true; 
	}
	else if (obj.attachEvent)
	{   
		var r = obj.attachEvent("on"+evType, fn); 	
		WD.EventCache.add(obj, evType, fn);
		return r;
	}
	else 
	{
		//Mac IE5 sucks here
		return false;
	} 
},

removeEvent:function (obj, evType, fn)
{ 
	if (obj.removeEventListener){  obj.removeEventListener(evType, fn, false);  return true; }
	else if (obj.detachEvent){   var r = obj.detachEvent("on"+evType, fn);    return r; }
	else { return false; } 
},

getObjById:function(id)
{
	return document.getElementById(id);
},


getChildByClass:function(el,tagName,className)
{
	var els = el.getElementsByTagName(tagName);
	className = className.split(" ");
	for( var i=0;i<els.length;i++)
	{
		for(var c=0;c<className.length;c++)
		{
			if( els[i].className.trim() == className[c].trim() ){ return els[i] ;}
		}		
	}
	return null;
},




getChildByName:function(el,tagName,Name)
{
	var els = el.getElementsByTagName(tagName);
	className = className.split(" ");
	for( var i=0;i<els.length;i++)
	{
		if( els[i].Name == Name){ return els[i] ;}
	}
	return null;
},


getNextObjByTagName:function(el,tagName)
{
	while(el && el.nextSibling)
	{
		el = el.nextSibling;
		if(el.nodeName.toLowerCase()==tagName.toLowerCase()){return el;}
	}		
	return null;
},


getNextPreviousByTagName:function(el,tagName)
{
	while(el && el.previousSibling)
	{
		el = el.previousSibling;
		if(el.nodeName.toLowerCase()==tagName.toLowerCase()){return el;}
	}		
	return null;
},



getObjByClass:function (el,tagName,className,level)
{
	level = level || 1000;
	var p= 0;
	var exit = function(el){ return ( (p>level) || (el==null) || (el.parentNode==null) || (el.tagName==null) || (el.className==null) || (el==document.body) );}
		
	while( !exit(el)  )
	{			
			if( (el.tagName.toLowerCase() == tagName) )
			{
				var c = el.className.split(" ");
				for(var i=0;i<c.length;i++)
				{
					if( (c[i]==className) || (className==""))
					{
						return el;
					}
				}
				
			}
			el = el.parentNode;
			p++;
	}
	return null;
},



getObjByName:function (el,tagName,Name,level)
{
	level = level || 1000;
	var p= 0;
	var exit = function(el){ return ( (p>level) || (el==null) || (el.parentNode==null) || (el.tagName==null) || (el.name==null) || (el==document.body) );}
		
	while( !exit(el)  )
	{			
			if( (el.tagName.toLowerCase() == tagName) && (el.name == Name) )
			{;return el;};			
			el = el.parentNode;
			p++;
	}
	return null;
},





getDimension:function(el)
{
 var d = new Object();
 if(el.getBoundingClientRect)
	{       
	   d.x = el.getBoundingClientRect().left + Math.max(document.body.scrollLeft, document.documentElement.scrollLeft);
	   d.y = el.getBoundingClientRect().top + Math.max(document.body.scrollTop, document.documentElement.scrollTop);
	   d.w = el.getBoundingClientRect().right - el.getBoundingClientRect().left;
	   d.h =  el.getBoundingClientRect().bottom - el.getBoundingClientRect().top;
	}
	else if(document.getBoxObjectFor)
	{
       d.x = document.getBoxObjectFor(el).x;
	   d.y =  document.getBoxObjectFor(el).y;
	   d.w = document.getBoxObjectFor(el).width;
	   d.h = document.getBoxObjectFor(el).height;
	}
	else
	{
			
			function offsetBy(el, type)
			{
			  if (this===el) return 0;
			  var v=999, owner=this, border='client'+type;
			  type = 'offset'+type;
			  do { v += owner[type];  } while ((owner=owner.offsetParent) && owner!==el && (v+=owner[border]))
			  return v-999;
			}
			
			d.x = offsetBy.call(el, null, 'Left');
			d.y= offsetBy.call(el, null, 'Top');
			d.w = el.offsetWidth;
			d.h = el.offsetHeight;
	
	}
	return d;
},

isTagName:function (el,tagName)
{
 return (el.nodeName.toLowerCase() == tagName.toLowerCase() );
},

hasClass:function (el,className)
{
 var c = el.className.split(" ");
 for(var i=0;i<c.length;i++)
	{
		if(c[i] == className){return true;};
	}
	return false;
},



getEvent:function (e)
{
	e = window.event ||e;
	e.leftButton=false;
	
	if(e.srcElement==null && e.target!=null)
	{	
		e.srcElement = e.target ;
		e.leftButton = ( e.button==1);		
	}
	else if(e.target==null && e.srcElement!=null)
	{ 
		e.target = e.srcElement;
		e.leftButton = ( e.button==0);
	}
	else if(e.srcElement!=null && e.target!=null)
	{
		//opera sucks and have both e.srcElement & e.target.
	}
	else{return null}

	var scrollLeft = 0;
	var scrollTop  = 0;
	if (document.compatMode && document.compatMode != "BackCompat")
	{
		scrollLeft = document.documentElement.scrollLeft;
		scrollTop  = document.documentElement.scrollTop;
	}
	else
	{
		scrollLeft = document.body.scrollLeft;
		scrollTop  = document.body.scrollTop;
	}
	e.mouseX = e.pageX || (e.clientX + scrollLeft);
	e.mouseY = e.pageY || (e.clientY + scrollTop);
	return e;
},



stopEvent:function(e)
{
	if(e && e.cancelBubble!=null)
	{
		e.cancelBubble = true;
		e.returnValue = false;
	}
	if(e && e.stopPropagation && e.preventDefault)
	{
		e.stopPropagation(); 
		e.preventDefault(); 
	}
	return false;
},


addClass:function(el,className)
{
	var c = el.className.split(" ");
	for(var i=0;i<c.length;i++)
	{
		if(c[i]==className){return;};
	}
	if(c.length>0)
	{
		el.className = (el.className + " " +className).trim();
	}
	else
	{
		el.className = className.trim();
	}
},


removeClass:function(el,className)
{
	var c = el.className.split(" ");
	for(var i=0;i<c.length;i++)
	{
		if(c[i]==className){c[i]="";};
	}
	el.className = c.join(" ").trim();
	
}


/*endof functions==============================*/
}

WD.EventCache = function()
{
	var listEvents = [];
	
	return {
		listEvents : listEvents,
	
		add : function(node, sEventName, fHandler, bCapture){listEvents[listEvents.length] = arguments;},
		
		flush : function(){
			var i, item;
			for(i = listEvents.length - 1; i >= 0; i = i - 1)
			{
				item = listEvents[i];				
				if(item[0].removeEventListener){item[0].removeEventListener(item[1], item[2], item[3]);};			
				if(item[1].substring(0, 2) != "on"){	item[1] = "on" + item[1];};				
				if(item[0].detachEvent){item[0].detachEvent(item[1], item[2]);};				
				item[0][item[1]] = null;
			};
		}
	};
}();


WD.addEvent(window,"unload",WD.EventCache.flush);
WD.browser["ie"] =  (document.all!=null)  && (window.opera==null); 
WD.browser["ie4"]  =  WD.browser["ie"] && (document.getElementById==null); 
WD.browser["ie5"]  =   WD.browser["ie"] && (document.namespaces==null) && (!WD.browser["ie4"]) ; 
WD.browser["ie6"]  =  WD.browser["ie"] && (document.implementation!=null) && (document.implementation.hasFeature!=null) 
WD.browser["ie55"]  =  WD.browser["ie"] && (document.namespaces!=null) && (!WD.browser["ie6"]); 
WD.browser["ns4"]  = !WD.browser["ie"] &&  (document.layers !=null) &&  (window.confirm !=null) && (document.createElement ==null); 
WD.browser["opera"] =  (self.opera!=null); 
WD.browser["gecko"] =  (document.getBoxObjectFor!=null); 
WD.browser["khtml"] = (navigator.vendor =="KDE"); 
WD.browser["konq"] =  ((navigator.vendor == 'KDE')||(document.childNodes)&&(!document.all)&&(!navigator.taintEnabled)); 
WD.browser["safari"] = (document.childNodes)&&(!document.all)&&(!navigator.taintEnabled)&&(!navigator.accentColorName); 
WD.browser["safari1.2"] = (parseInt(0).toFixed==null) && (WD.browser["safari"] && (window.XMLHttpRequest!=null)); 
WD.browser["safari2.0"] = (parseInt(0).toFixed!=null) && WD.browser["safari"] && !WD.browser["safari1.2"] ;
WD.browser["safari1.1"] = WD.browser["safari"] && !WD.browser["safari1.2"]  &&!WD.browser["safari2.0"] ;

for(i in self.WD)
{
 if(self[i]==null)
	{
		self[i] = self.WD[i];//synchronize for faster deelopement
	}
}


var ui_accordion = new Object();

ui_accordion["mouseout"] = function ()
{
	removeClass(this,"hover");
	return false;
}

ui_accordion["mousedown"] = function ()
{
	this.timer = setTimeout("void(0)",0);
	var k = 0;			
	var mb = this.mb;
	
	
	var dds = mb.parentNode.getElementsByTagName("dd");
	var dts = mb.parentNode.getElementsByTagName("dt");
	
	if (this.parentNode.getAttribute("standalone")!=null)
	{
		var do_expand = (mb.offsetHeight>1)?false:true;
		var action = function()
		{
			clearTimeout(this.timer);
			if (do_expand)
			{//do expand
				
				if (	mb.offsetHeight < (mb.scrollHeight-5) )
				{
					mb.style.height = Math.ceil( mb.offsetHeight +  (mb.scrollHeight-mb.offsetHeight)/7) + "px";
					this.timer = setTimeout(action,0);
				}
				else
				{
					mb.style.height="auto";
					mb.style.overflow ="visible";
					do_expand = null;
					action = null;
					mb = null;				
				}
			}
			else
			{//do collapse;

				if (parseInt(mb.style.height) > 3)
				{
					
					mb.style.height = Math.ceil(  parseInt(mb.style.height) +  (- parseInt(mb.style.height))/2) + "px";
					window.status =  mb.style.height + new Date();
					this.timer = setTimeout(action,0);
				}
				else
				{
					mb.style.height="0";
					mb.style.overflow ="hidden";
					mb.style.display = "none";
					do_expand = null;
					action = null;
					mb = null;				
				}
			}
		}

		if (do_expand)
		{//begin expand
			mb.style.height="1px";
			mb.style.overflow ="auto";
			mb.style.display="block";
		}
		else
		{//begin collapse;

			mb.style.height= mb.offsetHeight + "px";
			mb.style.overflow ="hidden";
			mb.style.display="block";
		}
		clearTimeout(this.timer);
		this.timer = setTimeout(action,0);
		return false;
	}
	
	
	var hasExpendedDD = false;
	for(var i=0;i<dds.length;i++)
	{
		dds[i].ddIndex = i;
		var closed,expand,collapse ;
		var collapsed = false;
		
		if (dds[i] == mb)
		{
			if (dts[mb.ddIndex])
			{
				addClass(dts[mb.ddIndex],"expanding");
			}
		
			var start = new Date();
			expand = function()
			{	
				clearTimeout(this.timer);
				if (mb.style.display.toLowerCase()!="block")
				{
					mb.style.display="block";
					mb.endHeight =  mb.scrollHeight;
					mb.style.overflow ="hidden";			
				}

				var end = new Date();

				if ((mb.offsetHeight < mb.endHeight) && ((end - start)<500))
				{
					mb.style.height = Math.ceil( mb.offsetHeight + (mb.endHeight- mb.offsetHeight)/33 ) + "px";
					this.timer = setTimeout(expand,0);				
				}
				else
				{	
					mb.style.height="auto";
					mb.style.overflow="auto";					
					if (dts[mb.ddIndex])
					{
						removeClass(dts[mb.ddIndex],"expanding");
					}
				}
				
			}
			
			
		}
		else if(dds[i].offsetHeight>0)
		{
			hasExpendedDD = true;
			collapsed = true;
			closed = dds[i];
			closed.style.height = closed.offsetHeight+"px";
			closed.style.overflow ="hidden";
			addClass(closed,"collapsing");
		
			if (dts[closed.ddIndex])
			{
				addClass(dts[closed.ddIndex],"collapsing");
			}
			var start = new Date();
			collapse = function()
			{
				
				clearTimeout(this.timer);
				var ph = parseInt(closed.style.height);
				var end = new Date();
				if ((ph > 2) && ((end - start)<200))
				{
					closed.style.height =Math.floor(ph  + (0-ph)/2)  + "px";
					this.timer = setTimeout(collapse,0);
				}
				else
				{	
					closed.style.height="0";
					closed.style.display="none";
					closed.style.overflow ="visible";
					if (dts[closed.ddIndex])
					{
						removeClass(dts[closed.ddIndex],"collapsing");
						removeClass(closed,"collapsing");
					}

					this.timer = setTimeout(expand,0);
				}
			}
			clearTimeout(this.timer);
			this.timer = setTimeout( collapse,0);
		}
		
	}
	if (collapsed==false)
	{
		if (dts[mb.ddIndex])
		{
			removeClass(dts[mb.ddIndex],"expanding");
		}
	}

	if (hasExpendedDD == false)
	{
		for(var i=0;i<dds.length;i++)
		{
			closed = dds[i];
			closed.style.height = 0;
			closed.style.overflow ="visible";
			removeClass(closed,"default_close");
			closed.style.display="none";	
			
			
		}
	
		this.timer = setTimeout(expand,0);
	}
	return false;
}

ui_accordion["mouseover"] = function (e,dt,doExpand)
{
	var el;
	if (dt)
	{
		el = dt;
	}
	else
	{
		e =  getEvent(e);
		if (e != null)
		{
			el =  getObjByClass(e.target,"dt","",2) ;
		}
	}

	if (el && el.parentNode && hasClass(el.parentNode,"accordion"))
	{
		var  def = getChildByClass(el.parentNode,"dd","default",1);

		if (!def)
		{
			if (el.parentNode.getElementsByTagName("dd").length>0)
			{
				var dd = el.parentNode.getElementsByTagName("dd")[0];
				addClass(dd ,"default");
				addClass(dd ,"default_close");
			}
			else
			{
				return;
			}
		}

		if (!doExpand)
		{
			addClass(el,"hover");
		}

		if (el.init==null)
		{
			var mb = getNextObjByTagName(el,"dd");
			if (mb==null)
			{
				return;
			}

			el.mb = mb;
			el.init = true;
			el.onmouseout =ui_accordion["mouseout"];
			el.onmousedown =ui_accordion["mousedown"];
			el.expand = ui_accordion["mousedown"];
		}
		
		if (doExpand)
		{
			el.expand();
		}
		if (e)
		{
			return stopEvent(e);
		}
	}
}

var init = addEvent(document,"mouseover",ui_accordion["mouseover"]);
if (init)
{
	document.write("<style media=\"screen\"> dl.accordion dd{height:0;display:none;}; </style>");
}

function accordion_menu_expand(accordion_header_id)
{
	var el  = document.getElementById(accordion_header_id);
	if (!el) {
		return false;
	}
	if (el.tagName.toLowerCase()!="dt")
	{
		return false;
	}
	ui_accordion["mouseover"](null,el,true);
}
//*******************************************************************************
//Title:      FCP Combo-Chromatic Color Picker
//URL:        http://www.free-color-picker.com
//Product No. FCP201a
//Version:    1.2
//Date:       10/01/2006
//NOTE:       Permission given to use this script in ANY kind of applications IF
//            script code remains UNCHANGED and the anchor tag "powered by FCP"
//            remains valid and visible to the user.
//
//  Call:     showColorGrid2("input_field_id","span_id")
//  Add:      <DIV ID="COLORPICKER201" CLASS="COLORPICKER201"></DIV> anywhere in body
//*******************************************************************************
function getScrollY(){var scrOfX = 0,scrOfY=0;if(typeof(window.pageYOffset)=='number'){scrOfY=window.pageYOffset;scrOfX=window.pageXOffset;}else if(document.body&&(document.body.scrollLeft||document.body.scrollTop)){scrOfY=document.body.scrollTop;scrOfX=document.body.scrollLeft;}else if(document.documentElement&&(document.documentElement.scrollLeft||document.documentElement.scrollTop)){scrOfY=document.documentElement.scrollTop;scrOfX=document.documentElement.scrollLeft;}return scrOfY;}document.write("<style type='text/css'>.colorpicker201{visibility:hidden;display:none;position:absolute;background:#FFF;border:solid 1px #CCC;padding:4px;z-index:999;filter:progid:DXImageTransform.Microsoft.Shadow(color=#D0D0D0,direction=135);}.o5582brd{padding:0;width:12px;height:14px;border-bottom:solid 1px #DFDFDF;border-right:solid 1px #DFDFDF;}a.o5582n66,.o5582n66,.o5582n66a{font-family:arial,tahoma,sans-serif;text-decoration:underline;font-size:9px;color:#666;border:none;}.o5582n66,.o5582n66a{text-align:center;text-decoration:none;}a:hover.o5582n66{text-decoration:none;color:#FFA500;cursor:pointer;}.a01p3{padding:1px 4px 1px 2px;background:whitesmoke;border:solid 1px #DFDFDF;}</style>");function getTop2(){csBrHt=0;if(typeof(window.innerWidth)=='number'){csBrHt=window.innerHeight;}else if(document.documentElement&&(document.documentElement.clientWidth||document.documentElement.clientHeight)){csBrHt=document.documentElement.clientHeight;}else if(document.body&&(document.body.clientWidth||document.body.clientHeight)){csBrHt=document.body.clientHeight;}ctop=((csBrHt/2)-115)+getScrollY();return ctop;}var nocol1="&#78;&#79;&#32;&#67;&#79;&#76;&#79;&#82;",clos1="&#67;&#76;&#79;&#83;&#69;",tt2="&#70;&#82;&#69;&#69;&#45;&#67;&#79;&#76;&#79;&#82;&#45;&#80;&#73;&#67;&#75;&#69;&#82;&#46;&#67;&#79;&#77;",hm2="&#104;&#116;&#116;&#112;&#58;&#47;&#47;&#119;&#119;&#119;&#46;";hm2+=tt2;tt2="&#80;&#79;&#87;&#69;&#82;&#69;&#68;&#32;&#98;&#121;&#32;&#70;&#67;&#80;";function getLeft2(){var csBrWt=0;if(typeof(window.innerWidth)=='number'){csBrWt=window.innerWidth;}else if(document.documentElement&&(document.documentElement.clientWidth||document.documentElement.clientHeight)){csBrWt=document.documentElement.clientWidth;}else if(document.body&&(document.body.clientWidth||document.body.clientHeight)){csBrWt=document.body.clientWidth;}cleft=(csBrWt/2)-125;return cleft;}function setCCbldID2(objID,val){document.getElementById(objID).value=val;}function setCCbldSty2(objID,prop,val){switch(prop){case "bc":if(objID!='none'){document.getElementById(objID).style.backgroundColor=val;};break;case "vs":document.getElementById(objID).style.visibility=val;break;case "ds":document.getElementById(objID).style.display=val;break;case "tp":document.getElementById(objID).style.top=val;break;case "lf":document.getElementById(objID).style.left=val;break;}}function putOBJxColor2(OBjElem,Samp,pigMent){if(pigMent!='x'){setCCbldID2(OBjElem,pigMent);setCCbldSty2(Samp,'bc',pigMent);}setCCbldSty2('colorpicker201','vs','hidden');setCCbldSty2('colorpicker201','ds','none');}function showColorGrid2(OBjElem,Sam){var objX=new Array('00','33','66','99','CC','FF');var c=0;var z='"'+OBjElem+'","'+Sam+'",""';var xl='"'+OBjElem+'","'+Sam+'","x"';var mid='';mid+='<table bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" style="border:solid 0px #F0F0F0;padding:2px;"><tr>';mid+="<td colspan='18' align='left' style='font-size:10px;background:#6666CC;color:#FFF;font-family:arial;'>&nbsp;Chromatic Selection Palette</td></tr><tr><td colspan='18' align='center' style='margin:0;padding:2px;height:12px;' ><input class='o5582n66' type='text' size='12' id='o5582n66' value='#FFFFFF'><input class='o5582n66a' type='text' size='2' style='width:14px;' id='o5582n66a' onclick='javascript:alert(\"click on selected swatch below...\");' value='' style='border:solid 1px #666;'>&nbsp;|&nbsp;<a class='o5582n66' href='javascript:onclick=putOBJxColor2("+z+")'><span class='a01p3'>"+nocol1+"</span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a class='o5582n66' href='javascript:onclick=putOBJxColor2("+xl+")'><span class='a01p3'>"+clos1+"</span></a></td></tr><tr>";var br=1;for(o=0;o<6;o++){mid+='</tr><tr>';for(y=0;y<6;y++){if(y==3){mid+='</tr><tr>';}for(x=0;x<6;x++){var grid='';grid=objX[o]+objX[y]+objX[x];var b="'"+OBjElem+"', '"+Sam+"','#"+grid+"'";mid+='<td class="o5582brd" style="background-color:#'+grid+'"><a class="o5582n66"  href="javascript:onclick=putOBJxColor2('+b+');" onmouseover=javascript:document.getElementById("o5582n66").value="#'+grid+'";javascript:document.getElementById("o5582n66a").style.backgroundColor="#'+grid+'";  title="#'+grid+'"><div style="width:12px;height:14px;"></div></a></td>';c++;}}}mid+="</tr><tr><td colspan='18' align='right' style='padding:2px;border:solid 1px #FFF;background:#FFF;'><a href='"+hm2+"' style='color:#666;font-size:8px;font-family:arial;text-decoration:none;letter-spacing:1px;'>"+tt2+"</a></td></tr></table>";var ttop=getTop2();setCCbldSty2('colorpicker201','tp',ttop);document.getElementById('colorpicker201').style.left=getLeft2();setCCbldSty2('colorpicker201','vs','visible');setCCbldSty2('colorpicker201','ds','block');document.getElementById('colorpicker201').innerHTML=mid;}
/*

CHECKTREE v1.0 RC (c) 2004-2006 Angus Turnbull, http://www.twinhelix.com
Altering this notice or redistributing this file is prohibited.

*/

function CheckTree(myName){this.myName=myName;this.root=null;this.countAllLevels=false;this.checkFormat='(%n% checked)';this.evtProcessed=navigator.userAgent.indexOf('Safari')>-1?'safRtnVal':'returnValue';CheckTree.list[myName]=this};CheckTree.list={};CheckTree.prototype.init=function(){with(this){if(!document.getElementById)return;root=document.getElementById('tree-'+myName);if(root){var lists=root.getElementsByTagName('ul');for(var ul=0;ul<lists.length;ul++){lists[ul].style.display='none';lists[ul].treeObj=this;lists[ul].setBoxStates=setBoxStates;var fn=new Function('e','this.setBoxStates(e)');if(lists[ul].addEventListener&&navigator.vendor!='Apple Computer,Inc.'){lists[ul].addEventListener('click',fn,false)}else lists[ul].onclick=fn}root.treeObj=this;root.setBoxStates=setBoxStates;if(root.addEventListener&&navigator.vendor!='Apple Computer,Inc.'){root.addEventListener('click',new Function('e',myName+'.click(e)'),false)}else root.onclick=new Function('e',myName+'.click(e)');root.setBoxStates({},true,true);var nodes=root.getElementsByTagName('li');for(var li=0;li<nodes.length;li++){if(nodes[li].id.match(/^show-/)){nodes[li].className=(nodes[li].className=='last'?'plus-last':'plus')}}}}};CheckTree.prototype.click=function(e){with(this){e=e||window.event;var elm=e.srcElement||e.target;if(!e[evtProcessed]&&elm.id&&elm.id.match(/^check-(.*)/)){var tree=document.getElementById('tree-'+RegExp.$1);if(tree)tree.setBoxStates(e,true,false)}while(elm){if(elm.tagName.match(/^(input|ul)/i))break;if(elm.id&&elm.id.match(/^show-(.*)/)){var targ=document.getElementById('tree-'+RegExp.$1);if(targ.style){var col=(targ.style.display=='none');targ.style.display=col?'block':'none';elm.className=elm.className.replace(col?'plus':'minus',col?'minus':'plus')}break}elm=elm.parentNode}}};function setBoxStates(e,routingDown,countOnly){with(this){if(!this.childNodes)return;e=e||window.event;var elm=e.srcElement||e.target;if(elm&&elm.id&&elm.id.match(/^check-(.*)/)&&!routingDown&&!e[treeObj.evtProcessed]){var refTree=document.getElementById('tree-'+RegExp.$1);if(refTree){refTree.setBoxStates(e,true,countOnly);e[treeObj.evtProcessed]=true}}var allChecked=true,boxCount=0,subBoxes=null;var thisLevel=this.id.match(/^tree-(.*)/)[1];var parBox=document.getElementById('check-'+thisLevel);for(var li=0;li<childNodes.length;li++){for(var tag=0;tag<childNodes[li].childNodes.length;tag++){var child=childNodes[li].childNodes[tag];if(!child)continue;if(child.tagName&&child.type&&child.tagName.match(/^input/i)&&child.type.match(/^checkbox/i)){if(routingDown&&parBox&&elm&&elm.id&&elm.id.match(/^check-/)&&!countOnly)child.checked=parBox.checked;allChecked&=child.checked;if(child.checked)boxCount++}if(child.tagName&&child.tagName.match(/^ul/i)&&(!e[treeObj.evtProcessed]||routingDown))child.setBoxStates(e,true,countOnly)}}if(!routingDown)e[treeObj.evtProcessed]=true;if(parBox&&parBox!=elm&&!countOnly)parBox.checked=allChecked;if(treeObj.countAllLevels){boxCount=0;var subBoxes=this.getElementsByTagName('input');for(var i=0;i<subBoxes.length;i++)if(subBoxes[i].checked)boxCount++}var countElm=document.getElementById('count-'+thisLevel);if(countElm){while(countElm.firstChild)countElm.removeChild(countElm.firstChild);if(boxCount)countElm.appendChild(document.createTextNode(treeObj.checkFormat.replace('%n%',boxCount)))}}};var chtOldOL=window.onload;window.onload=function(){if(chtOldOL)chtOldOL();for(var i in CheckTree.list)CheckTree.list[i].init()};/*!
Math.uuid.js (v1.4)
http://www.broofa.com
mailto:robert@broofa.com

Copyright (c) 2010 Robert Kieffer
Dual licensed under the MIT and GPL licenses.
*/

/*
 * Generate a random uuid.
 *
 * USAGE: Math.uuid(length, radix)
 *   length - the desired number of characters
 *   radix  - the number of allowable values for each character.
 *
 * EXAMPLES:
 *   // No arguments  - returns RFC4122, version 4 ID
 *   >>> Math.uuid()
 *   "92329D39-6F5C-4520-ABFC-AAB64544E172"
 * 
 *   // One argument - returns ID of the specified length
 *   >>> Math.uuid(15)     // 15 character ID (default base=62)
 *   "VcydxgltxrVZSTV"
 *
 *   // Two arguments - returns ID of the specified length, and radix. (Radix must be <= 62)
 *   >>> Math.uuid(8, 2)  // 8 character ID (base=2)
 *   "01001010"
 *   >>> Math.uuid(8, 10) // 8 character ID (base=10)
 *   "47473046"
 *   >>> Math.uuid(8, 16) // 8 character ID (base=16)
 *   "098F4D35"
 */
(function() {
  // Private array of chars to use
  var CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.split(''); 

  Math.uuid = function (len, radix) {
    var chars = CHARS, uuid = [];
    radix = radix || chars.length;

    if (len) {
      // Compact form
      for (var i = 0; i < len; i++) uuid[i] = chars[0 | Math.random()*radix];
    } else {
      // rfc4122, version 4 form
      var r;

      // rfc4122 requires these characters
      uuid[8] = uuid[13] = uuid[18] = uuid[23] = '-';
      uuid[14] = '4';

      // Fill in random data.  At i==19 set the high bits of clock sequence as
      // per rfc4122, sec. 4.1.5
      for (var i = 0; i < 36; i++) {
        if (!uuid[i]) {
          r = 0 | Math.random()*16;
          uuid[i] = chars[(i == 19) ? (r & 0x3) | 0x8 : r];
        }
      }
    }

    return uuid.join('');
  };

  // A more performant, but slightly bulkier, RFC4122v4 solution.  We boost performance
  // by minimizing calls to random()
  Math.uuidFast = function() {
    var chars = CHARS, uuid = new Array(36), rnd=0, r;
    for (var i = 0; i < 36; i++) {
      if (i==8 || i==13 ||  i==18 || i==23) {
        uuid[i] = '-';
      } else if (i==14) {
        uuid[i] = '4';
      } else {
        if (rnd <= 0x02) rnd = 0x2000000 + (Math.random()*0x1000000)|0;
        r = rnd & 0xf;
        rnd = rnd >> 4;
        uuid[i] = chars[(i == 19) ? (r & 0x3) | 0x8 : r];
      }
    }
    return uuid.join('');
  };

  // A more compact, but less performant, RFC4122v4 solution:
  Math.uuidCompact = function() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
      var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
      return v.toString(16);
    }).toUpperCase();
  };
})();
