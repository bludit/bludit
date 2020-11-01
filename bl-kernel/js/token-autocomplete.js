var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
var TokenAutocomplete = /** @class */ (function () {
    function TokenAutocomplete(options) {
        this.KEY_BACKSPACE = 8;
        this.KEY_ENTER = 13;
        this.KEY_UP = 38;
        this.KEY_DOWN = 40;
        this.defaults = {
            name: '',
            selector: '',
            noMatchesText: null,
            initialTokens: null,
            initialSuggestions: null,
            suggestionsUri: '',
            suggestionRenderer: TokenAutocomplete.Autocomplete.defaultRenderer,
            minCharactersForSuggestion: 1
        };
        this.options = __assign(__assign({}, this.defaults), options);
        var passedContainer = document.querySelector(this.options.selector);
        if (!passedContainer) {
            throw new Error('passed selector does not point to a DOM element.');
        }
        this.container = passedContainer;
        this.container.classList.add('token-autocomplete-container');
        if (!Array.isArray(this.options.initialTokens) && !Array.isArray(this.options.initialSuggestions)) {
            this.parseTokensAndSuggestions();
        }
        this.hiddenSelect = document.createElement('select');
        this.hiddenSelect.id = this.container.id + '-select';
        this.hiddenSelect.name = this.options.name;
        this.hiddenSelect.setAttribute('multiple', 'true');
        this.hiddenSelect.style.display = 'none';
        this.textInput = document.createElement('span');
        this.textInput.id = this.container.id + '-input';
        this.textInput.classList.add('token-autocomplete-input');
        this.textInput.setAttribute('data-placeholder', 'enter some text');
        this.textInput.contentEditable = 'true';
        this.container.appendChild(this.textInput);
        this.container.appendChild(this.hiddenSelect);
        this.select = new TokenAutocomplete.MultiSelect(this);
        this.autocomplete = new TokenAutocomplete.Autocomplete(this);
        this.debug(false);
        var me = this;
        if (Array.isArray(this.options.initialTokens)) {
            this.options.initialTokens.forEach(function (token) {
                if (typeof token === 'object') {
                    me.select.addToken(token.value, token.text);
                }
            });
        }
        this.textInput.addEventListener('keydown', function (event) {
            if (event.which == me.KEY_ENTER || event.keyCode == me.KEY_ENTER) {
                event.preventDefault();
                var highlightedSuggestion = me.autocomplete.suggestions.querySelector('.token-autocomplete-suggestion-highlighted');
                if (highlightedSuggestion !== null) {
                    if (highlightedSuggestion.classList.contains('token-autocomplete-suggestion-active')) {
                        me.select.removeTokenWithText(highlightedSuggestion.textContent);
                    }
                    else {
                        me.select.addToken(highlightedSuggestion.getAttribute('data-value'), highlightedSuggestion.textContent);
                    }
                }
                else {
                    me.select.addToken(me.textInput.textContent, me.textInput.textContent);
                }
                me.clearCurrentInput();
            }
            else if (me.textInput.textContent === '' && (event.which == me.KEY_BACKSPACE || event.keyCode == me.KEY_BACKSPACE)) {
                event.preventDefault();
                me.select.removeLastToken();
            }
        });
        this.textInput.addEventListener('keyup', function (event) {
            var _a, _b;
            if ((event.which == me.KEY_UP || event.keyCode == me.KEY_UP) && me.autocomplete.suggestions.childNodes.length > 0) {
                var highlightedSuggestion = me.autocomplete.suggestions.querySelector('.token-autocomplete-suggestion-highlighted');
                var aboveSuggestion = (_a = highlightedSuggestion) === null || _a === void 0 ? void 0 : _a.previousSibling;
                if (aboveSuggestion != null) {
                    me.autocomplete.highlightSuggestion(aboveSuggestion);
                }
                return;
            }
            if ((event.which == me.KEY_DOWN || event.keyCode == me.KEY_DOWN) && me.autocomplete.suggestions.childNodes.length > 0) {
                var highlightedSuggestion = me.autocomplete.suggestions.querySelector('.token-autocomplete-suggestion-highlighted');
                var belowSuggestion = (_b = highlightedSuggestion) === null || _b === void 0 ? void 0 : _b.nextSibling;
                if (belowSuggestion != null) {
                    me.autocomplete.highlightSuggestion(belowSuggestion);
                }
                return;
            }
            me.autocomplete.hideSuggestions();
            me.autocomplete.clearSuggestions();
            var value = me.textInput.textContent || '';
            if (value.length >= me.options.minCharactersForSuggestion) {
                if (Array.isArray(me.options.initialSuggestions)) {
                    me.options.initialSuggestions.forEach(function (suggestion) {
                        if (typeof suggestion !== 'object') {
                            // the suggestion is of wrong type and therefore ignored
                            return;
                        }
                        if (value.localeCompare(suggestion.text.slice(0, value.length), undefined, { sensitivity: 'base' }) === 0) {
                            // The suggestion starts with the query text the user entered and will be displayed
                            me.autocomplete.addSuggestion(suggestion);
                        }
                    });
                    if (me.autocomplete.suggestions.childNodes.length > 0) {
                        me.autocomplete.highlightSuggestionAtPosition(0);
                    }
                    else if (me.options.noMatchesText) {
                        me.autocomplete.addSuggestion({ value: '_no_match_', text: me.options.noMatchesText, description: null });
                    }
                }
                else if (me.options.suggestionsUri.length > 0) {
                    me.autocomplete.requestSuggestions(value);
                }
            }
        });
        this.container.tokenAutocomplete = this;
    }
    /**
     * Searches the element given as a container for option elements and creates active tokens (when the option is marked selected)
     * and suggestions (all options found) from these. During this all found options are removed from the DOM.
     */
    TokenAutocomplete.prototype.parseTokensAndSuggestions = function () {
        var initialTokens = [];
        var initialSuggestions = [];
        var options = this.container.querySelectorAll('option');
        var me = this;
        options.forEach(function (option) {
            if (option.text != null) {
                if (option.hasAttribute('selected')) {
                    initialTokens.push({ value: option.value, text: option.text });
                }
                initialSuggestions.push({ value: option.value, text: option.text, description: null });
            }
            me.container.removeChild(option);
        });
        if (initialTokens.length > 0) {
            this.options.initialTokens = initialTokens;
        }
        if (initialSuggestions.length > 0) {
            this.options.initialSuggestions = initialSuggestions;
        }
    };
    /**
     * Clears the currently present tokens and creates new ones from the given input value.
     *
     * @param {(Array\|string)} value - either the name of a single token or a list of tokens to create
     */
    TokenAutocomplete.prototype.val = function (value) {
        this.select.clear();
        if (Array.isArray(value)) {
            var me_1 = this;
            value.forEach(function (token) {
                if (typeof token === 'object') {
                    me_1.select.addToken(token.value, token.text);
                }
            });
        }
        else {
            this.select.addToken(value.value, value.text);
        }
    };
    TokenAutocomplete.prototype.clearCurrentInput = function () {
        this.textInput.textContent = '';
    };
    TokenAutocomplete.prototype.debug = function (state) {
        if (state) {
            this.log = console.log.bind(window.console);
        }
        else {
            this.log = function () { };
        }
    };
    var _a;
    TokenAutocomplete.MultiSelect = /** @class */ (function () {
        function class_1(parent) {
            this.parent = parent;
            this.container = parent.container;
            this.options = parent.options;
        }
        /**
         * Adds a token with the specified name to the list of currently prensent tokens displayed to the user and the hidden select.
         *
         * @param {string} tokenText - the name of the token to create
         */
        class_1.prototype.addToken = function (tokenValue, tokenText) {
            if (tokenValue === null || tokenText === null) {
                return;
            }
            var option = document.createElement('option');
            option.text = tokenText;
            option.value = tokenValue;
            option.setAttribute('selected', 'true');
            option.setAttribute('data-text', tokenText);
            option.setAttribute('data-value', tokenValue);
            this.parent.hiddenSelect.add(option);
            var token = document.createElement('span');
            token.classList.add('token-autocomplete-token');
            token.setAttribute('data-text', tokenText);
            option.setAttribute('data-value', tokenValue);
            token.textContent = tokenText;
            var deleteToken = document.createElement('span');
            deleteToken.classList.add('token-autocomplete-token-delete');
            deleteToken.textContent = '\u00D7';
            token.appendChild(deleteToken);
            var me = this;
            deleteToken.addEventListener('click', function (event) {
                me.removeToken(token);
            });
            this.container.insertBefore(token, this.parent.textInput.nextSibling);
            this.parent.log('added token', token);
        };
        /**
         * Completely clears the currently present tokens from the field.
         */
        class_1.prototype.clear = function () {
            var tokens = this.container.querySelectorAll('.token-autocomplete-token');
            var me = this;
            tokens.forEach(function (token) { me.removeToken(token); });
        };
        /**
         * Removes the last token in the list of currently present token. This is the last added token next to the input field.
         */
        class_1.prototype.removeLastToken = function () {
            var tokens = this.container.querySelectorAll('.token-autocomplete-token');
            var token = tokens[tokens.length - 1];
            this.removeToken(token);
        };
        /**
         * Removes the specified token from the list of currently present tokens.
         *
         * @param {Element} token - the token to remove
         */
        class_1.prototype.removeToken = function (token) {
            var _a, _b;
            this.container.removeChild(token);
            var tokenText = token.getAttribute('data-text');
            var hiddenOption = this.parent.hiddenSelect.querySelector('option[data-text="' + tokenText + '"]');
            (_b = (_a = hiddenOption) === null || _a === void 0 ? void 0 : _a.parentElement) === null || _b === void 0 ? void 0 : _b.removeChild(hiddenOption);
            this.parent.log('removed token', token.textContent);
        };
        class_1.prototype.removeTokenWithText = function (tokenText) {
            if (tokenText === null) {
                return;
            }
            var token = this.container.querySelector('.token-autocomplete-token[data-text="' + tokenText + '"]');
            if (token !== null) {
                this.removeToken(token);
            }
        };
        return class_1;
    }());
    TokenAutocomplete.Autocomplete = (_a = /** @class */ (function () {
            function class_2(parent) {
                this.parent = parent;
                this.container = parent.container;
                this.options = parent.options;
                this.renderer = parent.options.suggestionRenderer;
                this.suggestions = document.createElement('ul');
                this.suggestions.id = this.container.id + '-suggestions';
                this.suggestions.classList.add('token-autocomplete-suggestions');
                this.container.appendChild(this.suggestions);
            }
            /**
             * Hides the suggestions dropdown from the user.
             */
            class_2.prototype.hideSuggestions = function () {
                this.suggestions.style.display = '';
            };
            /**
             * Shows the suggestions dropdown to the user.
             */
            class_2.prototype.showSuggestions = function () {
                this.suggestions.style.display = 'block';
            };
            class_2.prototype.highlightSuggestionAtPosition = function (index) {
                var suggestions = this.suggestions.querySelectorAll('li');
                suggestions.forEach(function (suggestion) {
                    suggestion.classList.remove('token-autocomplete-suggestion-highlighted');
                });
                suggestions[index].classList.add('token-autocomplete-suggestion-highlighted');
            };
            class_2.prototype.highlightSuggestion = function (suggestion) {
                this.suggestions.querySelectorAll('li').forEach(function (suggestion) {
                    suggestion.classList.remove('token-autocomplete-suggestion-highlighted');
                });
                suggestion.classList.add('token-autocomplete-suggestion-highlighted');
            };
            /**
             * Removes all previous suggestions from the dropdown.
             */
            class_2.prototype.clearSuggestions = function () {
                this.suggestions.innerHTML = '';
            };
            /**
             * Loads suggestions matching the given query from the rest service behind the URI given as an option while initializing the field.
             *
             * @param query the query to search suggestions for
             */
            class_2.prototype.requestSuggestions = function (query) {
                var me = this;
                var request = new XMLHttpRequest();
                request.onload = function () {
                    if (Array.isArray(request.response)) {
                        request.response.forEach(function (suggestion) {
                            me.addSuggestion(suggestion);
                        });
                    }
                };
                request.open('GET', me.options.suggestionsUri + '?query=' + query, true);
                request.responseType = 'json';
                request.setRequestHeader('Content-type', 'application/json');
                request.send();
            };
            /**
             * Adds a suggestion with the given text matching the users input to the dropdown.
             *
             * @param {string} suggestionText - the text that should be displayed for the added suggestion
             */
            class_2.prototype.addSuggestion = function (suggestion) {
                var element = this.renderer(suggestion);
                element.setAttribute('data-value', suggestion.value);
                var me = this;
                element.addEventListener('click', function (_event) {
                    if (suggestion.text == me.options.noMatchesText) {
                        return true;
                    }
                    if (element.classList.contains('token-autocomplete-suggestion-active')) {
                        me.parent.select.removeTokenWithText(suggestion.text);
                    }
                    else {
                        me.parent.select.addToken(suggestion.value, suggestion.text);
                    }
                    me.clearSuggestions();
                    me.hideSuggestions();
                    me.parent.clearCurrentInput();
                });
                if (this.container.querySelector('.token-autocomplete-token[data-text="' + suggestion.text + '"]') !== null) {
                    element.classList.add('token-autocomplete-suggestion-active');
                }
                this.suggestions.appendChild(element);
                this.showSuggestions();
                me.parent.log('added suggestion', suggestion);
            };
            return class_2;
        }()),
        _a.defaultRenderer = function (suggestion) {
            var option = document.createElement('li');
            option.textContent = suggestion.text;
            if (suggestion.description) {
                var description = document.createElement('small');
                description.textContent = suggestion.description;
                description.classList.add('token-autocomplete-suggestion-description');
                option.appendChild(description);
            }
            return option;
        },
        _a);
    return TokenAutocomplete;
}());
//# sourceMappingURL=token-autocomplete.js.map