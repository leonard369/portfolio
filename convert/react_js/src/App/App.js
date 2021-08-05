import React from 'react';
import '../Header/Header.css';
import Header from '../Header/Header';
import '../Calc/Calc.css';
import Calc from '../Calc/Calc';

class  App extends React.Component {

render(){
  return (
<div id="App">
<Header />
<Calc />
</div>
    );
  }
}
export default App;
