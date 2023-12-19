import {Controller} from "@hotwired/stimulus";

export default class extends Controller{

	path = window.location.pathname;

	patterns = [
		{pattern: /\/organizations/, pathname: "Organisations", path: "organizations"},
		{pattern: /^\/organization$/, pathname: "Ajouter un organisation", path: "organization"},
		// contain "/organization" and "/edit"
		{pattern: /\/organization\/\d+\/edit/, pathname: "Modifier un organisation", path: "organization"},
	]; 

	connect(){
		this.createPath();
		this.isLastPath();
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

				const pathLink = document.createElement("a");

				pathLink.href = `/${pattern.path}`;
				pathLink.appendChild(document.createTextNode(pattern.pathname));
				pathEl.appendChild(pathLink);
				this.element.appendChild(pathEl);
			}
		});
	
		this.isLastPath();
	}

	isLastPath(){
		const paths = document.querySelectorAll(".path");
		const lastPath = paths[paths.length - 1];
	
		paths.forEach(path => {
			path.classList.remove("text-blighter-grey");
		});

		lastPath.classList.add("text-blighter-grey");
	}
}
