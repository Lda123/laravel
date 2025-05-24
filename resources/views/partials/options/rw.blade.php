<option value="">Pilih RW</option>
@foreach($rw as $item)
    <option value="{{ $item->id }}">RW {{ $item->nomor_rw }}</option>
@endforeach
