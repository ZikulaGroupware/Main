// Copyright Zikula Foundation 2010 - license GNU/LGPLv3 (or at your option, any later version).
function blocksmodifyinit(){Sortable.create("assignedblocklist",{dropOnEmpty:true,only:"z-sortable",containment:["assignedblocklist","unassignedblocklist"],onUpdate:blockorderchanged});Sortable.create("unassignedblocklist",{dropOnEmpty:true,only:"z-sortable",containment:["assignedblocklist","unassignedblocklist"]});initactivationbuttons();$A(document.getElementsByClassName("z-sortable")).each(function(b){var a=b.id.split("_")[1];Element.addClassName("block_"+a,"z-itemsort")})}function initactivationbuttons(){$$("a.activationbutton").each(function(a){a.removeClassName("activationbutton")})}function blockorderchanged(){var a="position="+$F("position")+"&"+Sortable.serialize("assignedblocklist",{name:"blockorder"});new Zikula.Ajax.Request("ajax.php?module=Blocks&func=changeblockorder",{parameters:a,onComplete:blockorderchanged_response})}function blockorderchanged_response(a){if(!a.isSuccess()){Zikula.showajaxerror(a.getMessage());return}Zikula.recolor("assignedblocklist","assignedblocklistheader");Zikula.recolor("unassignedblocklist","unassignedblocklistheader")}function toggleblock(a){var b="bid="+a;new Zikula.Ajax.Request("ajax.php?module=Blocks&func=toggleblock",{parameters:b,onComplete:toggleblock_response})}function toggleblock_response(a){if(!a.isSuccess()){Zikula.showajaxerror(a.getMessage());return}var b=a.getData();$("active_"+b.bid).toggle();$("inactive_"+b.bid).toggle();$("activity_"+b.bid).update((($("activity_"+b.bid).innerHTML==msgBlockStatusInactive)?msgBlockStatusActive:msgBlockStatusInactive))};