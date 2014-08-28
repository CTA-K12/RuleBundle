//Set the variables
var ruleset = {};
var ruleNames = {};

$(document).ready(function() {
    //Prep ht ruleset object
    ruleset.rulesetName = VAR_MESD_RULE_BUNDLE_RULESET_NAME;
    ruleset.ruleIndex = 0;
    ruleset.rules = {};

    //Start loading the rulesets
    $.ajax({
        url: ROUTE_MESD_RULE_BUNDLE_FORM_LOAD_FORM,
        type: 'POST',
        data: {'ruleset_name': ruleset.rulesetName}
    }).success(function(data) {
        //Set the ruleset data to the json from the controller
        ruleset = data;

        //Set the ruleNames array
        for (var r in ruleset.rules) {
            ruleNames[r] = ruleset.rules[r].name;
        }

        //Build the form
        buildForm();
    });

    //Save button
    $('#ruleFormSave').on('click', function(e) {
        //Stop the default
        e.preventDefault();

        //Clear out th eexisting errors
        $('.msgBlock').each(function() {
            $(this).remove();
        });

        //Send the ruleset object to the controller via a post
        $.ajax({
            url: ROUTE_MESD_RULE_BUNDLE_FORM_SAVE_FORM,
            type: 'POST',
            data: {'ruleset': ruleset}
        }).success(function(data) {
            if (false === data['success']) {
                for(var errorKey in data['errors']) {
                    if ('global' == errorKey) {
                        $('#errors-global').html(
                            '<div class="alert alert-danger msgBlock"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' + data['errors'][errorKey] + '</div>');
                    } else {
                        var error = errorKey.split('-');
                        if ('rule' == error[0]) {
                            $('#error-' + errorKey).html(
                                '<p class="text-danger msgBlock"><span class="glyphicon glyphicon-warning-sign"></span> ' + data['errors'][errorKey] + '</p>'
                            );
                        }
                    }
                }
                $('#ruleMessages').html(
                    '<div class="alert alert-danger msgBlock"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>Could not save, errors exist</div>');
            } else {
                $('#ruleMessages').html(
                    '<div class="alert alert-success msgBlock"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>Ruleset Saved</div>');
            }
        }).fail(function() {
            $('#ruleMessages').html(
                '<div class="alert alert-danger msgBlock"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>Could not save, errors exist</div>');
        });
    });

    //Button events
    $('#ruleFormNewRule').on('click', function(e) {
        //get the html block
        var block = getRule(ruleset.ruleIndex);

        //Add to underlying object
        ruleset.rules[ruleset.ruleIndex] = {
            'name': '',
            'collectionIndex': 1,
            'conditionIndex': 0,
            'actionIndex': 0,
            'followupRulesIndex': 0,
            'collections': {
                0: {
                    'chainType': 'any',
                    'parentIndex': -1
                }
            },
            'conditions': {},
            'actions': {},
            'followupRules': {}
        };

        //Create an entry for the name in the rule names
        ruleNames[ruleset.ruleIndex] = '';
        addToFollowupRuleSelectors(ruleset.ruleIndex, '');

        //Increment the index
        ruleset.ruleIndex++;

        //Append
        $('#ruleBlocks').append(block);
    });

    $('#ruleBlocks').on('click', '.ruleDelete', function(e) {
        //Delete from underlying object
        removeRule($(this).data('index'));

        //Remove it from the rule names array
        delete ruleNames[$(this).data('index')];
        removeFromFollowupRuleSelectors($(this).data('index'));

        //Remove from DOM
        $('#' + $(this).data('owner')).remove();
    });

    $('#ruleBlocks').on('click', '.addCondition', function(e) {
        //Stop the default
        e.preventDefault();

        //Add to the dom
        var pIndex = $(this).data('index');
        var rule = $(this).data('rule');
        var pName = $(this).data('name');

        //Register the addition in the underlying data
        ruleset.rules[rule].conditions[ruleset.rules[rule].conditionIndex] = {
            'attribute': {},
            'operatorValue': '',
            'inputValue': '',
            'parentCollection': pIndex
        };

        var block = getCondition(rule, pIndex, ruleset.rules[rule].conditionIndex);

        //Increment condition index
        ruleset.rules[rule].conditionIndex++;

        //append
        $('#collection' + pName + 'Conditions').append(block);
    });

    $('#ruleBlocks').on('click', '.removeCondition', function(e) {
        //Stop the default
        e.preventDefault();

        //Register the subtraction in the underlying data
        removeCondition($(this).data('rule'), $(this).data('index'));

        //remove from the dom
        $('#condition' + $(this).data('owner')).remove();
    });

    $('#ruleBlocks').on('click', '.addCollection', function(e) {
        //Stop the default
        e.preventDefault();

        //add to the dom
        var pIndex = $(this).data('index');
        var rule = $(this).data('rule');
        var pName = $(this).data('name');

        //Register the addition in the underlying data
        ruleset.rules[rule].collections[ruleset.rules[rule].collectionIndex] = {
            'chainType': 'any',
            'parentIndex': pIndex
        };

        var block = getConditionCollection(rule, pIndex, ruleset.rules[rule].collectionIndex);

        //Increment collection index
        ruleset.rules[rule].collectionIndex++;

        //append
        $('#collection' + pName + 'Conditions').append(block);

    });

    $('#ruleBlocks').on('click', '.removeCollection', function(e) {
        //Stop the default
        e.preventDefault();

        //Register the deletion in the underlying data
        removeCollection($(this).data('rule'), $(this).data('index'));

        //remove from the DOM
        $('#conditionCollection' + $(this).data('name')).remove();
    });

    $('#ruleBlocks').on('click', '.addThenAction', function(e) {
        //Stop the default
        e.preventDefault();

        //Get the variables
        var rule = $(this).data('rule');
        var index = ruleset.rules[rule].actionIndex;
        ruleset.rules[rule].actions[index] = {
            'type': 'then',
            'action': {},
            'inputValue': ''
        };

        var block = getAction(rule, index, 'then');

        ruleset.rules[rule].actionIndex++;

        $('#rule' + rule + 'ThenActions').append(block);
    });

    $('#ruleBlocks').on('click', '.addElseAction', function(e) {
        //Stop the default
        e.preventDefault();

        //Get the variables
        var rule = $(this).data('rule');
        var index = ruleset.rules[rule].actionIndex;
        ruleset.rules[rule].actions[index] = {
            'type': 'else',
            'action': {},
            'inputValue': ''
        };

        var block = getAction(rule, index, 'else');

        ruleset.rules[rule].actionIndex++;

        $('#rule' + rule + 'ElseActions').append(block);
    });

    $('#ruleBlocks').on('click', '.removeAction', function(e) {
        //Stop the default
        e.preventDefault();

        //Get the variables
        var name = $(this).data('owner');
        var rule = $(this).data('rule');
        var index = $(this).data('index');

        //Register the removal
        removeAction(rule, index);

        //remove from the dom
        $('#action' + name).remove();
    });

    //Add in the attribute listener
    $('#ruleBlocks').on('change', '.attributeSelector', function(e) {
        var selOpt = $('option:selected', this);
        var name = $(this).data('owner');
        var divName = 'condition' + name + 'Input';

        //get the attribute name and the context name if it exists
        var attrName = selOpt.data('attrname');
        var contextName = selOpt.data('contextname');

        if ('undefined' === typeof contextName) {
            contextName = '';
        }

        //Register this in the underlying ruleset
        var pieces = name.split('-');
        ruleset.rules[pieces[0]].conditions[pieces[1]].attribute.name = attrName;
        ruleset.rules[pieces[0]].conditions[pieces[1]].attribute.context = contextName;
        ruleset.rules[pieces[0]].conditions[pieces[1]].operatorValue = '';
        ruleset.rules[pieces[0]].conditions[pieces[1]].inputValue = '';

        //prep the url
        var url = ROUTE_MESD_RULE_BUNDLE_FORM_RENDER_COMPARATOR_AND_INPUT.replace(
            /__encodedAttributeName__/g, encodeURIComponent(attrName));
        url = url.replace(/__encodedContextName__/g, encodeURIComponent(contextName));
        if ('/' === url.slice(-1)) {
            url = url.substring(0, url.length - 1);
        }

        //Send the ajax request and load into the div with divName
        $('#' + divName).load(url, null, function() {
            $('#' + divName).html($('#' + divName).html().replace(/__name__/g, name));
            //Trigger the event to handle the operator value from the comparator
            $('#' + divName).find('.comparatorField').trigger('change');
        });
    });

    //Add in the action listener
    $('#ruleBlocks').on('change', '.actionSelector', function(e) {
        var selOpt = $('option:selected', this);
        var name = $(this).data('owner');
        var divName = 'action' + name + 'Input';

        //Get the action name and the context name if it exists
        var actionName = selOpt.data('actionname');
        var contextName = selOpt.data('contextname');

        if ('undefined' === typeof contextName) {
            contextName = '';
        }

        //Register this in the underlying ruleset
        var pieces = name.split('-');
        ruleset.rules[pieces[0]].actions[pieces[1]].action.name = actionName;
        ruleset.rules[pieces[0]].actions[pieces[1]].action.context = contextName;
        ruleset.rules[pieces[0]].actions[pieces[1]].inputValue = '';

        //prep the url
        var url = ROUTE_MESD_RULE_BUNDLE_FORM_RENDER_ACTION_INPUT.replace(
            /__encodedActionName__/g, encodeURIComponent(actionName));
        url = url.replace(/__encodedContextName__/g, encodeURIComponent(contextName));
        if ('/' === url.slice(-1)) {
            url = url.substring(0, url.length - 1);
        }

        //Send the ajax request and load into the div with divName
        $('#' + divName).load(url, null, function() {
            $('#' + divName).html($('#' + divName).html().replace(/__name__/g, name));
        });
    });

    //Update the actions on input change
    $('#ruleBlocks').on('keyup', '.actionInputField', function(e) {
        var pieces = $(this).data('owner').split('-');
        ruleset.rules[pieces[0]].actions[pieces[1]].inputValue = $(this).val();
    });

    //Update the attributes on input change
    $('#ruleBlocks').on('change', '.comparatorField', function(e) {
        var pieces = $(this).data('owner').split('-');
        ruleset.rules[pieces[0]].conditions[pieces[1]].operatorValue = $(this).val();
    });

    $('#ruleBlocks').on('keyup change', '.attributeInputField', function(e) {
        var pieces = $(this).data('owner').split('-');
        ruleset.rules[pieces[0]].conditions[pieces[1]].inputValue = $(this).val();
    });

    $('#ruleBlocks').on('keyup', '.ruleNameInput', function(e) {
        //Set the value in the array
        ruleset.rules[$(this).data('name')].name = $(this).val();

        //Update the value in the names array
        ruleNames[$(this).data('name')] = $(this).val();
        updateFollowupRuleSelectors($(this).data('name'), $(this).val());
    });

    $('#ruleBlocks').on('change', '.chainTypeSelector', function(e) {
        var rule = $(this).data('rule');
        var index = $(this).data('index');
        ruleset.rules[rule].collections[index].chainType = $(this).val();
    });

    $('#ruleBlocks').on('click', '.addThenRule', function(e) {
        //Prevent the default
        e.preventDefault();

        //Get the variables
        var rule = $(this).data('rule');

        //Get the index
        var index = ruleset.rules[rule].followupRulesIndex;
        ruleset.rules[rule].followupRulesIndex++;

        //Get the block
        var block = getFollowupRule(rule, index, 'then');

        //Add to the ruleset array
        ruleset.rules[rule].followupRules[index] = {
            'type': 'then',
            'name': ''
        };

        //Append
        $('#rule' + rule + 'ThenRules').append(block);

        //Set the initial value
        $('#followupSelector' + rule + '-' + index).trigger('change');
    });

    $('#ruleBlocks').on('click', '.addElseRule', function(e) {
        //Prevent the default
        e.preventDefault();

        //Get the variables
        var rule = $(this).data('rule');

        //Get the index
        var index = ruleset.rules[rule].followupRulesIndex;
        ruleset.rules[rule].followupRulesIndex++;

        //Get the block
        var block = getFollowupRule(rule, index, 'else');

        //Add to the ruleset array
        ruleset.rules[rule].followupRules[index] = {
            'type': 'else',
            'name': ''
        };

        //Append
        $('#rule' + rule + 'ElseRules').append(block);

        //Set the initial value
        $('#followupSelector' + rule + '-' + index).trigger('change');
    });

    $('#ruleBlocks').on('click', '.removeFollowupRule', function(e) {
        //Prevent the default
        e.preventDefault();

        //Get the variables
        var rule = $(this).data('rule');
        var index = $(this).data('index');

        //Register the removal
        removeFollowupRule(rule, index);

        //remove from the dom
        $('#followupRule' + rule + '-' + index).remove();
    });

    $('#ruleBlocks').on('change', '.followupSelector', function(e) {
        ruleset.rules[$(this).data('rule')].followupRules[$(this).data('index')].name = $(this).val();
    });
});


function buildForm() {
    //clear out the rule blocks area to be sure
    $('#ruleBlocks').empty();

    //foreach rule
    for(var rule in ruleset.rules) {
        //Get the rule block and append it
        $('#ruleBlocks').append(getRule(rule));

        //Set the name of the rule
        setRuleName(rule, ruleset.rules[rule].name);

        //Add in the condition collections
        setConditions(rule, 0);

        //Set the chain types
        for (var collection in ruleset.rules[rule].collections) {
            $('#chain' + rule + '-' + collection).val(ruleset.rules[rule].collections[collection].chainType);
        }

        //Set the condition values
        for (var condition in ruleset.rules[rule].conditions) {
            //get the attribute value
            var attr = ruleset.rules[rule].conditions[condition].attribute;
            var attrName = '';
            if (attr.context.length === 0 || !attr.context.trim()) {
                attrName = attr.name;
            } else {
                attrName = attr.context + '|' + attr.name;
            }
            $('#attributeSelector' + rule + '-' + condition).val(attrName);
            reloadAttributeInput(attr.name, attr.context, rule, condition,
                ruleset.rules[rule].conditions[condition].operatorValue, ruleset.rules[rule].conditions[condition].inputValue);
        }

        //load and set the actions
        for (var action in ruleset.rules[rule].actions) {
            //Load in the block
            var block = getAction(rule, action, ruleset.rules[rule].actions[action].type);

            //Append
            if ('then' == ruleset.rules[rule].actions[action].type) {
                $('#rule' + rule + 'ThenActions').append(block);
            } else {
                $('#rule' + rule + 'ElseActions').append(block);
            }

            //Get the action value
            var actObj = ruleset.rules[rule].actions[action].action;
            var actionName = '';
            if (actObj.context.length === 0 || !actObj.context.trim()) {
                actionName = actObj.name;
            } else {
                actionName = actObj.context + '|' + actObj.name;
            }
            $('#actionSelector' + rule + '-' + action).val(actionName);

            //Load and set the input
            reloadActionInput(actObj.name, actObj.context, rule, action, ruleset.rules[rule].actions[action].inputValue);
        }

        //Load and set the followup rules
        for (var followup in ruleset.rules[rule].followupRules) {
            //Load in the block
            var followBlock = getFollowupRule(rule, followup, ruleset.rules[rule].followupRules[followup].type);

            //Append
            if ('then' == ruleset.rules[rule].followupRules[followup].type) {
                $('#rule' + rule + 'ThenRules').append(followBlock);
            } else {
                $('#rule' + rule + 'ElseRules').append(followBlock);
            }

            //Set the input
            $('#followupSelector' + rule + '-' + followup).val(ruleset.rules[rule].followupRules[followup].name);
        }

        //Display the rule blocks
        $('#ruleBlocks').removeClass('hidden');

        //Hide the loading
        $('#loading').addClass('hidden');

        //enable the buttons
        $('#ruleFormNewRule').removeClass('disabled');
        $('#ruleFormSave').removeClass('disabled');
    }
}


function reloadAttributeInput(attrName, contextName, rule, condition, operatorValue, inputValue) {
    //Load and set the followup fields
    //prep the url
    var url = ROUTE_MESD_RULE_BUNDLE_FORM_RENDER_COMPARATOR_AND_INPUT.replace(
        /__encodedAttributeName__/g, encodeURIComponent(attrName));
    url = url.replace(/__encodedContextName__/g, encodeURIComponent(contextName));
    if ('/' === url.slice(-1)) {
        url = url.substring(0, url.length - 1);
    }

    //Send the ajax request and load into the div with divName
    var divName = '#condition' + rule + '-' + condition +'Input';
    $(divName).load(url, null, function() {
        $(divName).html($(divName).html().replace(/__name__/g, rule + '-' +condition));

        //Set the comparator
        $(divName).find('.comparatorField').val(operatorValue);

        //Set the input
        $(divName).find('.attributeInputField').val(inputValue);
    });
}

function reloadActionInput(actionName, contextName, rule, action, inputValue) {
    //prep the url
    var url = ROUTE_MESD_RULE_BUNDLE_FORM_RENDER_ACTION_INPUT.replace(
        /__encodedActionName__/g, encodeURIComponent(actionName));
    url = url.replace(/__encodedContextName__/g, encodeURIComponent(contextName));
    if ('/' === url.slice(-1)) {
        url = url.substring(0, url.length - 1);
    }

    //Send the ajax request and load into the div with divName
    var divName = '#action' + rule + '-' + action + 'Input';
    $(divName).load(url, null, function() {
        $(divName).html($(divName).html().replace(/__name__/g, rule + '-' + action));

        //set the input value
        $(divName).find('.actionInputField').val(inputValue);
    });
}


function removeCollection(rule, index) {
    //Delete the collection itself
    delete ruleset.rules[rule].collections[index];

    //Delete the conditions that have this as a parent
    for(var condition in ruleset.rules[rule].conditions) {
        if (ruleset.rules[rule].conditions[condition].parentCollection == index) {
            removeCondition(rule, condition);
        }
    }

    //Delete the collections that have this as a parent
    for(var collection in ruleset.rules[rule].collections) {
        if (ruleset.rules[rule].collections[collection].parentIndex == index) {
            removeCollection(rule, collection);
        }
    }
}


function getConditionCollection(rule, parent, index) {
    //Get the collection prototype
    var proto = $('#conditionCollectionPrototype').html();

    //Generate the name
    var name = rule + '-' + index;

    //Add in the variables
    var block = proto.replace(/__name__/g, name);
    block = block.replace(/__parent__/g, parent);
    block = block.replace(/__rule__/g, rule);
    block = block.replace(/__index__/g, index);

    if (0 === index) {
        block = block.replace(/__removeButton__/g, '');
    } else {
        block = block.replace(/__removeButton__/g, getCollectionRemoveButton(rule, index, name));
    }

    //Return
    return block;
}


function getCollectionRemoveButton(rule, index, name) {
    //Get the prototype
    var proto = $('#conditionCollectionRemoveButtonPrototype').html();

    //Add in the variables
    var block = proto.replace(/__name__/g, name);
    block = block.replace(/__rule__/g, rule);
    block = block.replace(/__index__/g, index);

    //return
    return block;
}


function getCondition(rule, parent, index) {
    //Get the condition prototype
    var proto = $('#conditionPrototype').html();

    //Generate the name
    var name = rule + '-' + index;

    //Add in the variables
    var block = proto.replace(/__name__/g, name);
    block = block.replace(/__owner__/g, parent);
    block = block.replace(/__rule__/g, rule);
    block = block.replace(/__index__/g, index);
    block = block.replace(/__attribute__/g, getAttributeSelector(name));

    //Return
    return block;
}


function getAttributeSelector(conditionName) {
    //Get the attribute selector prototype
    var proto = $('#attributeListPrototype').html();

    //Add in the variables
    var block = proto.replace(/__name__/g, conditionName);

    //return the block
    return block;
}


function getActionSelector(actionName) {
    //Get the action selector prototype
    var proto = $('#actionListPrototype').html();

    //Add in the variables
    var block = proto.replace(/__name__/g, actionName);

    //Return the block
    return block;
}


function getAction(rule, index, type) {
    //Get the prototype
    var proto = $('#actionPrototype').html();

    //Add in the variables
    var block = proto.replace(/__name__/g, rule + '-' + index);
    block = block.replace(/__index__/g, index);
    block = block.replace(/__rule__/g, rule);
    block = block.replace(/__type__/g, type);
    block = block.replace(/__action__/g, getActionSelector(rule + '-' + index));

    //Return the block
    return block;
}

function getRule(index) {
    //Append a new rule block onto the rules section
    var proto = $('#ruleBlockPrototype').html();

    //Add in the index
    var block = proto.replace(/__name__/g, index);

    //Create the root condition collection
    block = block.replace(/__rootCollection__/g, getConditionCollection(index, -1, 0));

    //return the block
    return block;
}

function setRuleName(index, name) {
    $('#rule' + index + 'NameInput').val(name);
}

function removeCondition(rule, index) {
    delete ruleset.rules[rule].conditions[index];
}

function removeAction(rule, index) {
    delete ruleset.rules[rule].actions[index];
}

function removeRule(index) {
    delete ruleset.rules[index];
}

function setConditions(rule, parent) {
    //Set the conditions foreach condition that has the given parent
    for (var condition in ruleset.rules[rule].conditions) {
        //Check if the condition has this for a parent
        if (parent == ruleset.rules[rule].conditions[condition].parentCollection) {
            //Get the block
            var conBlock = getCondition(rule, parent, condition);

            //Append
            $('#collection' + rule + '-' + parent + 'Conditions').append(conBlock);
        }
    }

    //Set the collections foreach collection that has the given parent
    for (var collection in ruleset.rules[rule].collections) {
        //Check if this collection has this parent
        if (parent == ruleset.rules[rule].collections[collection].parentIndex) {
            //Get the block
            var colBlock = getConditionCollection(rule, parent, collection);

            //Append
            $('#collection' + rule + '-' + parent + 'Conditions').append(colBlock);

            //Recursive call
            setConditions(rule, collection);
        }
    }
}

function getFollowupRule(rule, index, type) {
    //Get the prototype
    var proto = $('#followupRulePrototype').html();

    //Add in the variables
    var block = proto.replace(/__index__/g, index);
    block = block.replace(/__rule__/g, rule);
    block = block.replace(/__type__/g, type);
    block = block.replace(/__ruleSelector__/g, getFollowupRuleSelector(rule, index));

    //return the block
    return block;
}


function getFollowupRuleSelector(rule, index) {
    //Get the prototype
    var proto = $('#ruleSelectorPrototype').html();

    //Add in the variables
    var block = proto.replace(/__index__/g, index);
    block = block.replace(/__rule__/g, rule);

    //Generate the options
    var options = '';
    for(var i in ruleNames) {
        options = options + '<option value="' + ruleNames[i] + '" data-index="'+ i +'">' + ruleNames[i] + '</option>';
    }
    block = block.replace(/__options__/g, options);

    //Return the block
    return block;
}


function removeFollowupRule(rule, index) {
    delete ruleset.rules[rule].followupRules[index];
}

function removeFromFollowupRuleSelectors(index) {
    $('.followupSelector').each(function(i) {
        //Ignore the prototype
        if ('__rule__' != $(this).data('rule')) {
            $(this).find('option').each(function(j) {
                if (index == $(this).data('index')) {
                    $(this).remove();
                }
            });
        }
    });
}

function updateFollowupRuleSelectors(index, newName) {
    $('.followupSelector').each(function(i) {
        $(this).find('option').each(function(j) {
            if ($(this).data('index') == index) {
                $(this).val(newName);
                $(this).html(newName);
            }
        });
    });
}

function addToFollowupRuleSelectors(index, name) {
    $('.followupSelector').each(function(i) {
        //Add a check to make sure the prototype wasnt grabbed
        if ('__rule__' != $(this).data('rule')) {
            $(this).append('<option value="' + name + '" data-index="'+ index +'">' + name + '</option>');
        }
    });
}