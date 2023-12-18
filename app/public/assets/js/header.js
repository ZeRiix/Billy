const path = window.location.pathname;
const container = document.getElementById("path");

switch (path){
	  case "/dashboard":
		addPath("Accueil");
		break;
	  case "/dashboard/my-organization":
		addPath("Accueil/Mon organisation");
		break;
	  case "/dashboard/organizations":
		addPath("Accueil/Mes organisations");
		break;
	  case "/organization":
		addPath("Accueil/Mes organisations/Ajouter");
}

function addPath(path){
	const paths = path.split("/");

	paths.forEach(function callback(path, index){
		const pathEl = document.createElement("li");

		pathEl.className = "path";
		pathEl.appendChild(document.createTextNode(path));
		container.appendChild(pathEl);

		if(paths.length > 1 && index < paths.length - 1){
			const arrow = document.createElement("li");

			arrow.appendChild(document.createTextNode(">"));
			container.appendChild(arrow);
		}
	  });

	isLastPath();
}

function isLastPath(){
	const paths = document.querySelectorAll(".path");
	const lastPath = paths[paths.length - 1];
	
	lastPath.classList.add("text-blighter-grey");
}
