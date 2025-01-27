<div class="row d-flex justify-content-between mb-5">
    @if($searchable)
        <div class="col-4">
            <label for="search">جستوجو</label>
            <input id="search" type="text" class="form-control " placeholder="{{$placeholder ?? ''}}" wire:model.live="search">
        </div>
    @endif
    <div class="col-6 d-flex {{ $searchable ? 'justify-content-end' : '' }} ">
        <div class="col-6 p-0">
            <label for="per-page">صفحه بندی</label>
            <select id="per-page" class="form-control" wire:model.live="per_page">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        @if($sortable)
            <div class="col-6 px-2 p-0">
                <label for="per-page">مرتب سازی</label>
                <select id="per-page" class="form-control " wire:model.live="direction">
                    <option value="desc">نزولی</option>
                    <option value="asc">صعودی</option>
                </select>
            </div>
        @endif
    </div>
</div>
