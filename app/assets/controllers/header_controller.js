import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
	url = window.location.pathname;

	patterns = [
		{
			pattern: /^\/organization$/,
			pathname: "Ajouter un organisation",
			path: null,
		},
		{
			pattern: /^\/organization\/[a-z0-9-]+/,
			pathname: "Organisation",
			path: null,
		},
		{
			pattern: /^\/organization\/[a-z0-9-]+\/edit$/,
			pathname: "Modifier un organisation",
			path: null,
		},
		{
			pattern: /^\/organization\/[a-z0-9-]+\/roles$/,
			pathname: "Gérer les rôles",
			path: null,
		},
		{
			pattern: /^\/organization\/[a-z0-9-]+\/role$/,
			pathname: "Créer un rôle",
			path: null,
		},
		{
			pattern: /^\/organization\/[a-z0-9-]+\/role\/[a-z0-9-]+$/,
			pathname: "Modifier le rôle",
			path: null,
		},
		{
			pattern: /^\/organization\/[a-z0-9-]+\/services$/,
			pathname: "Gérer les services",
			path: null,
		},
		{
			pattern: /^\/organization\/[a-z0-9-]+\/clients$/,
			pathname: "Gérer les clients",
			path: null,
		},
		{
			pattern: /^\/organization\/[a-z0-9-]+\/client\/[a-z0-9-]+$/,
			pathname: "Modifier le client",
			path: null,
		},
		{
			pattern: /^\/organization\/[a-z0-9-]+\/users$/,
			pathname: "Gérer les utilisateurs",
			path: null,
		},
	];

	connect() {
		const matchedPatterns = this.patterns.filter(({ pattern }) => pattern.test(this.url));

		matchedPatterns.forEach((pattern, index, array) => {
			const separator = document.createElement("li");
			separator.appendChild(document.createTextNode(" > "));
			this.element.appendChild(separator);

			const pathEl = document.createElement("li");
			pathEl.classList.add("path");

			if (index === array.length - 1 || pattern.path === null) {
				pathEl.appendChild(document.createTextNode(pattern.pathname));
			} else {
				const pathLink = document.createElement("a");
				pathLink.href = `${pattern.path || "#"}`;
				pathLink.appendChild(document.createTextNode(pattern.pathname));
				pathEl.appendChild(pathLink);
			}

			this.element.appendChild(pathEl);
			this.isLastPath();
		});
	}

	isLastPath() {
		const paths = document.querySelectorAll(".path");
		const lastPath = paths[paths.length - 1];

		paths.forEach((path) => {
			path.classList.remove("text-blighter-grey");
		});

		lastPath.classList.add("text-blighter-grey");
	}
}