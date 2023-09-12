//función para obtener el height del elemento más largo
function setMaxHeightCard(){
	var cards = document.getElementsByTagName('card')

	console.log(cards)


	var cards = document.querySelectorAll('.card')
	var maxHeight = 0
	//obtener el height más grande para asignar a la clase card su respectivo max-height
	cards.forEach(function(element, index, array){
		if (maxHeight < element.offsetHeight) {
			maxHeight = element.offsetHeight
		}
	    console.log("height: "+element.offsetHeight)
	  });

	//asignar a todos los cards con el mismo height tomando el mayor de todos ellos
	cards.forEach(function(element, index, array){
		element.style.height = maxHeight+'px'
	  });

}