import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto';

export default class extends Controller {
	connect() {
		if([new Date(this.query["from"]).toString(), new Date(this.query["to"]).toString()].includes("Invalid Date")){
			const from = new Date();
			const to = new Date();
			from.setMonth(from.getMonth() - 1);
			to.setDate(to.getDate() + 1);
			window.location.search = `?from=${from.toISOString().split("T")[0]}&to=${to.toISOString().split("T")[0]}`
			return
		}

		document.getElementById("inputFrom").value = this.query["from"]
		document.getElementById("inputTo").value = this.query["to"]

		const service = JSON.parse(this.data.get("serviceValue"));
		const devisStatus = JSON.parse(this.data.get("statusDevisValue"));
		const completedDevis = JSON.parse(this.data.get("completedDevisValue"));

		new Chart(
			document.getElementById("pieServiceSum"),
			{
				type: "pie",
				data: {
					labels: service.map(v => v.name),
					datasets: [{
						data: service.map(v => v.quantity),
					}],
				},
				options: {
					plugins: {
						title: {
							display: true,
							text: `Service les plus vendue`
						}
					}
				}
			}
		)

		new Chart(
			document.getElementById("pieStatusDevis"),
			{
				type: "pie",
				data: {
					labels: devisStatus.map(v => v.status),
					datasets: [{
						data:devisStatus.map(v => v.count),
					}],
				},
				options: {
					plugins: {
						title: {
							display: true,
							text: `Status des devis`
						}
					}
				}
			}
		)

		new Chart(
			document.getElementById("lineCompletedDevis"),
			{
				type: "line",
				data: {
					labels: completedDevis.map(v => v.date.split("-").toReversed().join("/")),
					datasets: [{
						label: "devis complété",
						data: completedDevis.map(v => v.count),
					}],
				},
				options: {
					plugins: {
						title: {
							display: true,
							text: `Devis compléter`
						}
					}
				}
			}
		)

		console.log(completedDevis);
	}

	get query(){
		return window
		.location
		.search
		.substring(1)
		.split("&")
		.reduce((pv, cv) => {
			const [key, value] = cv.split("=");
			pv[key] = value;
			return pv;
		}, {});
	}
}