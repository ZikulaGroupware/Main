// Copyright Zikula Foundation 2010 - license GNU/LGPLv3 (or at your option, any later version).
function permissioninit(){appending=false;deleteiconhtml=$("permeditdelete_"+adminpermission).innerHTML;canceliconhtml=$("permeditcancel_"+adminpermission).innerHTML;if(adminpermission!=-1){if($("permission_"+adminpermission)){$("permission_"+adminpermission).addClassName("adminpermission")}else{alert("admin permission not found!");adminpermission=-1}}$A(document.getElementsByClassName("z-sortable","permissionlist")).each(function(b){var a=b.id.split("_")[1];if(lockadmin==1&&a==adminpermission){$("permission_"+a).title=permissionlocked;$("permdrag_"+a).update("("+a+")");$("permission_"+a).addClassName("permlocked");$("permission_"+a).removeClassName("z-sortable")}else{$("permission_"+a).addClassName("normalpermission");$("permission_"+a).addClassName("z-itemsort");$("permdrag_"+a).update("("+a+")");$("modifyajax_"+a).removeClassName("z-hide");$("modifyajax_"+a).observe("click",function(){permmodifyinit(a)})}$("insert_"+a).addClassName("z-hide");$("modify_"+a).addClassName("z-hide");$("delete_"+a).addClassName("z-hide");$("testpermajax_"+a).removeClassName("z-hide");$("testpermajax_"+a).observe("click",function(){permtestinit(a)})});$$("button.z-imagebutton").each(function(b){var a=b.id.split("_")[1];switch(b.id.split("_")[0]){case"permeditsave":$("permeditsave_"+a).observe("click",function(){permmodify(a)});break;case"permeditdelete":$("permeditdelete_"+a).observe("click",function(){permdelete(a)});break;case"permeditcancel":$("permeditcancel_"+a).observe("click",function(){permmodifycancel(a)});break}});$("appendajax").removeClassName("z-hide");if($("permgroupfilterform")){$("permgroupfilterform").action="javascript:void(0);";$("permgroupfiltersubmit").remove("z-hide");$("permgroupfiltersubmitajax").removeClassName("z-hide")}$("testpermsubmit").remove();$("testpermsubmitajax").removeClassName("z-hide");$("testpermform").action="javascript:void(0);";$("permissiondraganddrophint").removeClassName("z-hide");Sortable.create("permissionlist",{only:"z-sortable",onUpdate:sortorderchanged})}function permappend(){if(appending==false){appending=true;new Zikula.Ajax.Request("ajax.php?module=Permissions&func=createpermission",{onComplete:permappend_response})}}function permappend_response(b){appending=false;if(!b.isSuccess()){Zikula.showajaxerror(b.getMessage());return}var c=b.getData();var a=$("permission_1").cloneNode(true);a.id="permission_"+c.pid;$A(a.getElementsByTagName("div")).each(function(d){d.id=d.id.split("_")[0]+"_"+c.pid});$A(a.getElementsByTagName("span")).each(function(d){d.id=d.id.split("_")[0]+"_"+c.pid});$A(a.getElementsByTagName("input")).each(function(d){d.id=d.id.split("_")[0]+"_"+c.pid;d.value=""});$A(a.getElementsByTagName("select")).each(function(d){d.id=d.id.split("_")[0]+"_"+c.pid});$A(a.getElementsByTagName("button")).each(function(d){d.id=d.id.split("_")[0]+"_"+c.pid});$A(a.getElementsByTagName("textarea")).each(function(d){d.id=d.id.split("_")[0]+"_"+c.pid});$("permissionlist").appendChild(a);$("permission_"+c.pid).removeClassName("adminpermission");$("permission_"+c.pid).removeClassName("permlocked");$("groupid_"+c.pid).value=c.gid;$("levelid_"+c.pid).value=c.level;$("sequence_"+c.pid).value=c.sequence;$("component_"+c.pid).value=c.component;$("instance_"+c.pid).value=c.instance;Zikula.setselectoption("group_"+c.pid,c.gid);Zikula.setselectoption("level_"+c.pid,c.level);$("permeditcancel_"+c.pid).addClassName("z-hide");$("permeditdelete_"+c.pid).update(canceliconhtml);$("permdrag_"+c.pid).update("("+c.pid+")");$("permcomp_"+c.pid).update(c.component);$("perminst_"+c.pid).update(c.instance);$("permgroup_"+c.pid).update(c.groupname);$("permlevel_"+c.pid).update(c.levelname);$("modifyajax_"+c.pid).stopObserving("click");$("testpermajax_"+c.pid).stopObserving("click");$("permeditsave_"+c.pid).stopObserving("click");$("permeditdelete_"+c.pid).stopObserving("click");$("permeditcancel_"+c.pid).stopObserving("click");$("modifyajax_"+c.pid).observe("click",function(){permmodifyinit(c.pid)});$("testpermajax_"+c.pid).observe("click",function(){permtestinit(c.pid)});$("permeditsave_"+c.pid).observe("click",function(){permmodify(c.pid)});$("permeditdelete_"+c.pid).observe("click",function(){permdelete(c.pid)});$("permeditcancel_"+c.pid).observe("click",function(){permmodifycancel(c.pid)});$("permission_"+c.pid).addClassName("z-sortable");$("permission_"+c.pid).addClassName("normalpermission");$("permission_"+c.pid).addClassName("z-itemsort");$("modifyajax_"+c.pid).removeClassName("z-hide");$("modifyajax_"+c.pid).observe("click",function(){permmodifyinit(c.pid)});enableeditfields(c.pid);$("permission_"+c.pid).removeClassName("z-hide");new Effect.Highlight("permission_"+c.pid,{startcolor:"#ffff99",endcolor:"#ffffff"});Sortable.create("permissionlist",{only:"z-sortable",constraint:false,onUpdate:sortorderchanged})}function permtestinit(a){$("test_user").value="";$("test_component").value=$("permcomp_"+a).innerHTML;$("test_instance").value=$("perminst_"+a).innerHTML;Zikula.setselectoption("test_level",$F("levelid_"+a));$("permissiontestinfo").update("&nbsp;");$("testpermform").scrollTo()}function sortorderchanged(){$("permission_"+adminpermission).addClassName("z-sortable");var a=Sortable.serialize("permissionlist",{name:"permorder"});$("permission_"+adminpermission).removeClassName("z-sortable");new Zikula.Ajax.Request("ajax.php?module=Permissions&func=changeorder",{parameters:a,onComplete:sortorderchanged_response})}function sortorderchanged_response(a){if(!a.isSuccess()){Zikula.showajaxerror(a.getMessage());return}Zikula.recolor("permissionlist","permlistheader")}function permmodifyinit(a){if(getmodifystatus(a)==0){Zikula.setselectoption("group_"+a,$F("groupid_"+a));Zikula.setselectoption("level_"+a,$F("levelid_"+a));enableeditfields(a)}}function permmodifycancel(a){disableeditfields(a)}function permmodify(a){if(a==adminpermission&&lockadmin==1){return}disableeditfields(a);if(getmodifystatus(a)==0){setmodifystatus(a,1);showinfo(a,updatingpermission);var b={pid:a,gid:$F("group_"+a),seq:$F("sequence_"+a),comp:$F("component_"+a),inst:$F("instance_"+a),level:$F("level_"+a)};new Zikula.Ajax.Request("ajax.php?module=Permissions&func=updatepermission",{parameters:b,onComplete:permmodify_response})}}function permmodify_response(a){if(!a.isSuccess()){Zikula.showajaxerror(a.getMessage());showinfo();return}var b=a.getData();$("groupid_"+b.pid).value=b.gid;$("levelid_"+b.pid).value=b.level;$("permgroup_"+b.pid).update(b.groupname);$("permcomp_"+b.pid).update(b.component);$("editpermcomp_"+b.pid,b.component);$("perminst_"+b.pid).update(b.instance);$("editperminst_"+b.pid,b.instance);$("permlevel_"+b.pid).update(b.levelname);$("permeditcancel_"+b.pid).removeClassName("z-hide");$("permeditdelete_"+b.pid).update(deleteiconhtml);$("permeditcancel_"+b.pid).observe("click",function(){permmodifycancel(b.pid)});setmodifystatus(b.pid,0);showinfo(b.pid)}function permdelete(a){if(a==adminpermission&&lockadmin==1){return}if(confirm(confirmdeleteperm)&&getmodifystatus(a)==0){showinfo(a,deletingpermission);setmodifystatus(a,1);var b={pid:a};new Zikula.Ajax.Request("ajax.php?module=Permissions&func=deletepermission",{parameters:b,onComplete:permdelete_response})}}function permdelete_response(a){if(!a.isSuccess()){Zikula.showajaxerror(a.getMessage());return}var b=a.getData();$("permission_"+b.pid).remove()}function enableeditfields(a){$("permgroup_"+a).addClassName("z-hide");$("permcomp_"+a).addClassName("z-hide");$("perminst_"+a).addClassName("z-hide");$("permlevel_"+a).addClassName("z-hide");$("permaction_"+a).addClassName("z-hide");$("editpermgroup_"+a).removeClassName("z-hide");$("editpermcomp_"+a).removeClassName("z-hide");$("editperminst_"+a).removeClassName("z-hide");$("editpermlevel_"+a).removeClassName("z-hide");$("editpermaction_"+a).removeClassName("z-hide")}function disableeditfields(a){$("editpermgroup_"+a).addClassName("z-hide");$("editpermcomp_"+a).addClassName("z-hide");$("editperminst_"+a).addClassName("z-hide");$("editpermlevel_"+a).addClassName("z-hide");$("editpermaction_"+a).addClassName("z-hide");$("permgroup_"+a).removeClassName("z-hide");$("permcomp_"+a).removeClassName("z-hide");$("perminst_"+a).removeClassName("z-hide");$("permlevel_"+a).removeClassName("z-hide");$("permaction_"+a).removeClassName("z-hide")}function getmodifystatus(a){return $F("modifystatus_"+a)}function setmodifystatus(b,a){$("modifystatus_"+b).value=a}function showinfo(c,a){if(c){var b=$("permissioninfo_"+c);var d=$("permissioncontent_"+c);if(!b.hasClassName("z-hide")){b.update("&nbsp;");b.addClassName("z-hide");d.removeClassName("z-hide")}else{b.update(a);d.addClassName("z-hide");b.removeClassName("z-hide")}}else{$A(document.getElementsByClassName("permissioninfo")).each(function(e){$(e).update("&nbsp;");$(e).addClassName("z-hide")});$A(document.getElementsByClassName("permissioncontent")).each(function(e){$(e).removeClassName("z-hide")})}}function permgroupfilter(){var a=$F("permgrp");filtertype=a.split("+")[0];filter=a.split("+")[1];if(filtertype=="g"){$("filterwarningcomponent").hide();if(filter==-1){$("filterwarninggroup").hide()}else{$("filterwarninggroup").show()}$A(document.getElementsByClassName("z-sortable")).each(function(b){permid=b.id.split("_")[1];if(filter==-1){$("permission_"+permid).show()}else{groupid=$F("groupid_"+permid);if(groupid!=filter&&groupid!=-1){$("permission_"+permid).hide()}else{$("permission_"+permid).show()}}})}else{if(filtertype=="c"){$("filterwarninggroup").hide();if(filter==-1){$("filterwarningcomponent").hide()}else{$("filterwarningcomponent").show()}$A(document.getElementsByClassName("z-sortable")).each(function(b){permid=b.id.split("_")[1];if(filter==-1){$("permission_"+permid).show()}else{permcomp=$F("comp_"+permid);if(permcomp.indexOf(filter)==0||permcomp==".*"){$("permission_"+permid).show()}else{$("permission_"+permid).hide()}}})}}if(filter==-1){$("filterwarning").hide()}else{$("filterwarning").show()}}function performpermissiontest(){$("permissiontestinfo").update(testingpermission);var a=Form.serialize("testpermform");Form.disable("testpermform");new Zikula.Ajax.Request("ajax.php?module=Permissions&func=testpermission",{parameters:a,onComplete:performpermissiontest_response})}function performpermissiontest_response(a){Form.enable("testpermform");$("permissiontestinfo").update("&nbsp;");if(!a.isSuccess()){Zikula.showajaxerror(a.getMessage());return}var b=a.getData();$("permissiontestinfo").update(b.testresult)};