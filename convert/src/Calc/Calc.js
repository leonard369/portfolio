import React from 'react';
import './Calc.css';

class  Calc extends React.Component {
constructor(props){
  super(props);
this.state={
  'currencyRate':{}
}
this.valute=['USD','EUR','CAD'];
this.getRate()

}
  getRate=()=>{
    fetch('https://www.cbr-xml-daily.ru/daily_json.js')
    .then(data=>{
      return data.json();
    })
    .then(data=>{
      let arr= {};
      for(let i=0; i < this.valute.length; i++){
        arr[this.valute[i]]=data.Valute[this.valute[i]].Value;
      }
      this.setState({currencyRate: arr});
      
      var val= document.getElementById('input_one');
      var select_one=document.getElementById('select_one');
      var select_two=document.getElementById('select_two');
      var summ=document.getElementById('input_two');
      var button =document.getElementById('button');
      button.onclick=function()
      {
      changediv();
        
      }
      function changediv()
      {
         let i1= val.value;
         let i2= summ.value;
         let s1=select_one.value;
         let s2=select_two.value;
         document.getElementById('input_two').value=i1;
         document.getElementById('input_one').value=i2;
         const number = document.getElementsByClassName('select_one');
        const block  = document.getElementsByClassName('select_two');
        block.innerHTML = number.innerHTML;
      }
      console.log(arr);
      function calculation(){
        if(select_one.value===select_two.value){
          summ.value=val.value;
        }else{
            summ.value = Math.ceil((val.value*arr[select_two.value])*100)/100;  
        }
      }
      val.oninput = function(){
        calculation();
      };
      select_one.oninput = function(){
        calculation();
      };
      select_two.oninput = function(){
        calculation();
      };
    }); 
  }
render(){
  return (
<div id="Calc">
<div class="calc">
<div class="block">
<h1>Конвертер валют</h1>
</div>
<div class="block">
<div class="box">
<span class="text">Вы переводите</span>
<select id="select_one">
{Object.keys(this.state.currencyRate).map((keyName,i)=>
(
<option value={keyName} key={keyName}>{keyName}</option>
)
)}
</select>
<span class="text">в</span>
<select id="select_two">
{Object.keys(this.state.currencyRate).map((keyName,i)=>
(
<option value={keyName} key={keyName}>{keyName}</option>
)
)}
</select>
</div>
<div class="block"><div class="box"><span class="clear"></span>
<input type="text"  id="input_one" />
<span class="text">=</span>
<input type="text" id="input_two" />
</div></div>
</div>
<div class="block"><div class="box"><button id="button" onclick="changediv();" >Поменять местами</button></div></div>
</div>
</div>
    );
  }
}
export default Calc;
