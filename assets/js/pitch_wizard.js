$(function() {
	
	$("a.pw-order-detail").on("click", function(ev) {
		ev.preventDefault();
		var _this = $(this);
		var id = _this.data("id");
		var modal_id = _this.data("modal");
		var content_url = "admin/contact/pitch_wizard_order/load_order_detail_modal/" + id;

		var modal = $("#" + modal_id);
		var modal_content = modal.find(".modal-content");
		modal_content.load(content_url, function() {
			modal.modal('show');
		});						
	});	
	
});