import {Controller} from "@hotwired/stimulus";

export default class extends Controller{

	path = window.location.pathname;

	patterns = [
		{pattern: /\/organizations/, path: "Organisations"},
		{pattern: /^\/organization$/, path: "Ajouter un organisation"},
	]; 

	connect(){
		this.createPath();
	}

	createPath(){
		this.patterns.forEach((pattern, i) => {
			if(this.path.match(pattern.pattern)){
				console.log(i);

				if(i == 0 || i % 2 != 0){
					const arrow = document.createElement("li");

					arrow.appendChild(document.createTextNode(">"));
					this.element.appendChild(arrow);
				}

				const pathEl = document.createElement("li");
	
				pathEl.className = "path";
				pathEl.appendChild(document.createTextNode(pattern.path));
				this.element.appendChild(pathEl);
			}
		});
	
		this.isLastPath();
	}

	isLastPath(){
		const paths = document.querySelectorAll(".path");
		const lastPath = paths[paths.length - 1];
	
		lastPath.classList.add("text-blighter-grey");
	}
}
