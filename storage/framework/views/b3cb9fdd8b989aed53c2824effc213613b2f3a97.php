<script>
function addForm(){
   var addrow = '<div class="form-group baru-data">\
             <div class="col-md-3">\
                 <input type="text" name="nama_produk" placeholder="Nama Produk" class="form-control">\
             </div>\
             <div class="col-md-2">\
                 <input type="number" name="jumlah_produk" placeholder="Jumlah Produk" class="form-control">\
             </div>\
             <div class="col-md-3">\
               <select class="form-control">\
                  <option value="">- Pilih Kategori -</option>\
                  <option value="1">Buku</option>\
                  <option value="2">Elektronik</option>\
                  <option value="3">Kesehatan</option>\
                  <option value="4">Rumah Tangga</option>\
                  <option value="5">Mainan Hobi</option>\
                  <option value="6">Olahraga</option>\
               </select>\
             </div>\
             <div class="col-md-3">\
                 <textarea name="deskripsi_produk" placeholder="Deskripsi Produk" class="form-control" rows="1"></textarea>\
             </div>\
             <div class="button-group">\
                 <button type="button" class="btn btn-success btn-tambah"><i class="fa fa-plus"></i></button>\
                 <button type="button" class="btn btn-danger btn-hapus"><i class="fa fa-times"></i></button>\
             </div>\
      </div>'
   $("#dynamic_form").append(addrow);
}

$("#dynamic_form").on("click", ".btn-tambah", function(){
   addForm()
   $(this).css("display","none")     
   var valtes = $(this).parent().find(".btn-hapus").css("display","");
})

$("#dynamic_form").on("click", ".btn-hapus", function(){
 $(this).parent().parent('.baru-data').remove();
 var bykrow = $(".baru-data").length;
 if(bykrow==1){
   $(".btn-hapus").css("display","none")
   $(".btn-tambah").css("display","");
 }else{
   $('.baru-data').last().find('.btn-tambah').css("display","");
 }
});

$('.btn-simpan').on('click', function () {
   $('#dynamic_form').find('input[type="text"], input[type="number"], select, textarea').each(function() {
      if( $(this).val() == "" ) {
         event.preventDefault()
         $(this).css('border-color', 'red');
         
         $(this).on('focus', function() {
            $(this).css('border-color', '#ccc');
         });
      }
   })
})
</script><?php /**PATH /home/cobitdemoptsico/public_html/resources/views/quality_assurance_testing/dynamicForm.blade.php ENDPATH**/ ?>