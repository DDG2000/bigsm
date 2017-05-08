//money 贷款金额
//apr 年利率
//t  贷款时间
//ty 还款类型
function calc(money,apr,t,ty,cycle){  
  this.money=parseFloat(money);
  this.apr=parseFloat(apr);
  this.t=parseInt(t);
  this.ty=parseInt(ty);
  this.cycle=parseInt(cycle);
  this.da={s:0,list:[],total:0,month:0,day:0};
  this.exe();
}
calc.prototype={
  exe:function(){
	if(this.cycle==1){
		this.da.month=this.apr/1200;
	    switch(this.ty){
		  case 1:
		   this.da.s=this.money*this.da.month*Math.pow((1+this.da.month),this.t)/(Math.pow((1+this.da.month),this.t)-1);
		   this.da.list=[];
		   this.da.total=this.da.s*this.t;
		   var money=this.money;
		   var r_prin=money;
		   for(var i=1;i<=this.t;i++){
			 var ss={};
			 if(i<this.t){
				 ss.inte=(r_prin*this.da.month).toFixed(2);
				 ss.prin=(this.da.s-ss.inte).toFixed(2);
				 r_prin-=ss.prin;
			 }else{
				 ss.prin=(r_prin).toFixed(2);
				 ss.inte=(this.da.s-ss.prin).toFixed(2);
			 }
			 ss.money=(this.da.s).toFixed(2);
			 ss.sur=(this.da.total-i*this.da.s).toFixed(2);
			 this.da.list.push(ss);
		   }
		   break;
		   case 2:
		   this.da.s=this.money*this.da.month;
		   this.da.total=this.money*this.da.month*this.t+this.money;
		   for(var i=1;i<=this.t;i++){
			 var ss={};
			 if(i==this.t){
			   var money=this.da.total-i*this.da.s;
			   ss.inte=(this.da.s+money-this.money).toFixed(2);
			   ss.prin=this.money;
			   ss.money=(this.da.s+money).toFixed(2);
			   ss.sur=0;
			 }else{
			   ss.inte=(this.da.s).toFixed(2);
			   ss.prin=0;
			   ss.money=(this.da.s).toFixed(2);
			   ss.sur=(this.da.total-i*this.da.s).toFixed(2);
			 }
			 this.da.list.push(ss);
		   }
		   break;
		   case 3:
		   this.da.s=this.money*this.da.month;
		   this.da.total=this.da.s*this.t+this.money;
		   var ss={};
		   ss.inte=(this.da.s*this.t).toFixed(2);
		   ss.prin=this.money;
		   ss.money=(this.da.total).toFixed(2);
		   ss.sur=0;
		   this.da.list.push(ss);
		   break;
		  break;
		}
		this.da.earn=this.da.total-this.money;	  
  }else{
	  this.da.day=this.apr/36000;
	    switch(this.ty){		  
		   case 2:
		   this.da.s=this.money*this.da.day;
		   this.da.total=this.money*this.da.day*this.t+this.money;
		   var ss={};
		   ss.inte=(this.da.s*this.t).toFixed(2);
		   ss.prin=0;
		   ss.money=(this.da.s*this.t).toFixed(2);
		   ss.sur=this.money;
		   this.da.list.push(ss);
		   var ss={};
		   ss.inte=0;
		   ss.prin=this.money;
		   ss.money=(this.money).toFixed(2);
		   ss.sur=0;
		   this.da.list.push(ss);
		   break;
		   case 3:
		   this.da.s=this.money*this.da.day;
		   this.da.total=this.da.s*this.t+this.money;
		   var ss={};
		   ss.inte=(this.da.s*this.t).toFixed(2);
		   ss.prin=this.money;
		   ss.money=(this.da.total).toFixed(2);
		   ss.sur=0;
		   this.da.list.push(ss);
		   break;
		  break;
		}
		this.da.earn=this.da.total-this.money;
      }
	}
}