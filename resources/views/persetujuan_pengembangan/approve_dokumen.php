<!-- partials/form-modal.blade.php -->
<div class="modal fade" id="modalApproveDokumen" tabindex="-1" role="dialog" aria-labelledby="modalApproveDokumenLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalApproveDokumenLabel">Approve Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Tambahkan ID pada form -->
                <form method="POST" enctype="multipart/form-data" action="">
                        <div class="col-md-12">
                        <label>Tanda Tangan :</label>
                        <br/>
                        <canvas id="sig" width="400" height="200" style="border: 1px solid #000;"></canvas>
                            <br/><br/>
                            <button id="clear" class="btn btn-danger btn-sm">Clear</button>
                            <textarea id="signature" name="signed" style="display: none"></textarea>
                        </div>
                    <br/>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<script type="text/javascript">
    // Inisialisasi Signature Pad menggunakan Vanilla JavaScript
    var canvas = document.getElementById('sig');
    var signaturePad = new SignaturePad(canvas);

    document.getElementById('clear').addEventListener('click', function (e) {
        e.preventDefault();
        signaturePad.clear();
        document.getElementById("signature64").value = '';
    });
</script>