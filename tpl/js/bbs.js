var bbs={
	sear:function(k){
	  var v=$("#"+k).val();
	  if(v=="请输入关键字"){v="";}
	  v=v.replace(/^\s+/,"");
	  if(v==""){return;}
	  self.location.href="list.php?keyword="+encodeURIComponent(v);
	}
	}

function Csubstr(str, len) 
{ 
    var newLength = 0; 
    var newStr = ""; 
    var chineseRegex = /[^\x00-\xff]/g; 
    var singleChar = ""; 
    var strLength = str.replace(chineseRegex,"**").length; 
    for(var i = 0;i < strLength;i++){ 
        singleChar = str.charAt(i).toString(); 
        if(singleChar.match(chineseRegex) != null){ 
            newLength += 2; 
        }     
        else{ 
            newLength++; 
        } 
        if(newLength > len){ 
            break; 
        } 
        newStr += singleChar; 
    } 
    if(strLength > len){ 
        newStr += "..."; 
    } 
    return newStr; 
} 