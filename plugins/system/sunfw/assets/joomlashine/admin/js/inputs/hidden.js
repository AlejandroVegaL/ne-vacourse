var InputHidden=api.InputHidden=extendReactClass('MixinInput',{render:function(){return React.createElement("input",{ref:"control",type:"hidden",name:this.props.setting,value:this.state.value,onChange:this.change});}});