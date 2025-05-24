<option value="">Pilih RT</option>
@foreach($rt as $item)
    <option value="{{ $item->id }}">RT {{ $item->nomor_rt }}</option>
@endforeach
