import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
	signaturePad

	connect(){
		const openButton = document.getElementById("openButton")
		if(!openButton){
			return
		}

		openButton.addEventListener(
			"click", 
			() => {
				this.element.classList.remove("hidden")
				this.signaturePad = new SignaturePad(document.getElementById("signCanvas"), {
					backgroundColor: "rgb(255,255,255)"
				})
			} 
		)
		
		const closeButton = document.getElementById("closeButton")

		this.element.addEventListener(
			"click", 
			(event) => {
				if(event.target === this.element || event.target === closeButton){
					this.element.classList.add("hidden")
				}
			}
		)

		document
		.getElementById("clearButton")
		.addEventListener("click", () => {
			this.signaturePad.clear();
		})

		document
		.getElementById("doneButton")
		.addEventListener("click", () => {
			if(!this.signaturePad.toData()[0]){
				alert("Veuiller signer");
				return
			}
			const dataTransfer = new DataTransfer();
			dataTransfer.items.add(this.dataURLtoBlob(this.signaturePad.toDataURL("image/jpeg")));

			document.getElementById("sign_devis_form_imageSign_file").files = dataTransfer.files;
			document.querySelector("form[name=\'sign_devis_form\']").requestSubmit();
		})
	}

	dataURLtoBlob(dataurl){
		var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
			bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
		while(n--){
			u8arr[n] = bstr.charCodeAt(n);
		}
		return new File([u8arr], "sign", {type: mime});
	}
}