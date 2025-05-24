<option value="">Pilih Kelurahan</option>
@foreach($kelurahan as $kel)
    <option value="{{ $kel->id }}">{{ $kel->nama_kelurahan }}</option>
@endforeach
