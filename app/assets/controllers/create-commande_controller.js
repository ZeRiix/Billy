import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  services;
  selectElement;
  descriptionElement;
  unitPriceElement;
  nameElement;

  connect() {
    this.services = JSON.parse(this.data.get("servicesValue"));
    this.selectElement = document.getElementById(
      "create_commande_form_service"
    );
    this.nameElement = document.getElementById("create_commande_form_name");
    this.descriptionElement = document.getElementById(
      "create_commande_form_description"
    );
    this.unitPriceElement = document.getElementById(
      "create_commande_form_unitPrice"
    );
    console.log(this.services);
    this.selectElement.addEventListener("change", this.select.bind(this));
    this.select();
  }

  select() {
    const { description, name, unitPrice } = this.services.find(
      (v) => v.id === this.selectElement.value
    );

    this.nameElement.value = name;
    this.descriptionElement.value = description;
    this.unitPriceElement.value = unitPrice;
  }
}
