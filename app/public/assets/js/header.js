document.addEventListener("DOMContentLoaded", function () {
    const url = window.location.pathname;

    console.log(url + "/edit");

    const patterns = [
        { pattern: /^\/organization$/, pathname: "Ajouter un organisation", path: null },
        { pattern: /^\/organization\/[a-z0-9-]+/, pathname: "Organisation", path: url.slice(0, -5) },
        {
            pattern: /^\/organization\/[a-z0-9-]+\/edit$/,
            pathname: "Modifier un organisation",
            path: null,
        },
    ];

    const matchedPatterns = patterns.filter(({ pattern }) => pattern.test(url));

    const header = document.getElementById("header");

    matchedPatterns.forEach((pattern, index, array) => {
        const separator = document.createElement("li");
        separator.appendChild(document.createTextNode(" > "));
        header.appendChild(separator);

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

        header.appendChild(pathEl);
        isLastPath();
    });

    function isLastPath() {
        const paths = document.querySelectorAll(".path");
        const lastPath = paths[paths.length - 1];

        paths.forEach((path) => {
            path.classList.remove("text-blighter-grey");
        });

        lastPath.classList.add("text-blighter-grey");
    }
});
