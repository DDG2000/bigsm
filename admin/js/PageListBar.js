/*
    名称 : 页面分页导航
    用法示例:
        var plb = new PageListBar('plb', 343, 6, '?', 10);
        document.write(plb);
*/
function PageListBar(name, pagecount, currpage, url, listlength,rscount){
    this.name = name;
    this.pagecount = pagecount;
    this.currpage = currpage;
    this.url = url;
    this.listlength = listlength?listlength:5;
    this.rscount = rscount;  
    if(this.pagecount <=0){this.pagecount = 1;}
    if(this.currpage > this.pagecount){this.currpage = this.pagecount;}
    this.ppt = '';
	if(typeof(this.rscount) != 'undefined'){
		this.ppt = '共[<font class="blue">'+this.rscount+'</font>]条记录&nbsp;&nbsp;';
	}

    var str = '', pStart = pEnd = 1;
    if(this.pagecount <= 1){pStart = pEnd = 1;}else{if(this.pagecount <= this.listlength){pStart = 1;pEnd = this.pagecount;}else{var movestep = Math.round(this.listlength/2);if(this.currpage > movestep){pStart = this.currpage - movestep;pEnd = this.currpage + movestep;if(pEnd > this.pagecount){pEnd = this.pagecount;pStart=this.pagecount-this.listlength;}}else{pStart = 1;pEnd = this.listlength+1;}}}

	str += pStart>1?'<a class="apage" href="javascript:' + this.name + '.go(1);void(0);"><div>1..</div></a>':'';
	var npStart = pStart>1?pStart+1:pStart , npEnd = pEnd<this.pagecount?pEnd-1:pEnd;
    for(var i=npStart; i<=npEnd; i++){
        str += '<a href="javascript:' + this.name + '.go(' + i + ');void(0);" class="'+ (i==this.currpage?"apage_now":"apage") +'"><div>' + i + '</div></a>';
    }
	str += pEnd<this.pagecount?'<a class="apage" href="javascript:' + this.name + '.go('+ this.pagecount +');void(0);"><div>..'+this.pagecount+'</div></a>':'';
    str += '<div class="page_sum"> [<font class="blue">'+this.currpage+'</font>/<font class="green">'+this.pagecount+'</font>] 页</div><div class="page_jump">至<input id="jstopage" type="text" value="'+this.currpage+'" class="page_put1"/>页 </div><div class="page_jump"><a href="javascript:' + this.name + '.gotopage();void(0);" class="but">跳转</a></div>';

	this.pagestr = str;
}
PageListBar.prototype.go = function(pagenum){window.location.href = '?currpage=' + pagenum + this.url;}
PageListBar.prototype.gotopage = function(){
    var topage = document.getElementById('jstopage').value;
    if(isNaN(topage))topage=1;
    if(topage){
		if(topage>this.pagecount){topage=this.pagecount;}if(topage<1){topage=1;} 
		if(topage!=this.currpage){this.go(topage);}else{document.getElementById('jstopage').value=topage;}
	}
}
PageListBar.prototype.toString = function(){return this.pagestr;}
