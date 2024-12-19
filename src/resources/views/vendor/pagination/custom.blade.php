<style>
    svg.w-5.h-5 {
        /*paginateメソッドの矢印の大きさ調整のために追加*/
        width: 30px;
        height: 30px;
    }

    .pagination {
        list-style-type: none;
        /* 黒丸を非表示にする */
        padding: 0;
        margin: 20px;
        display: flex;
    }

    .pagination li {
        margin: 0 3px;
    }

    .pagination li a,
    .pagination li span {
        padding: 8px 12px;
        text-decoration: none;
        color: #007bff;
        /* リンク色 */
        border: 1px solid #dee2e6;
        /* 枠線 */
        border-radius: 4px;
    }

    .pagination li.active span {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .pagination li.disabled span {
        color: #6c757d;
    }
</style>

</style>

<!-- 日付を引き継ぐ &date={{ request()->date }} -->
<ul class="pagination">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
    <li class="pagination-item disabled"><span>&laquo;</span></li>
    @else
    <li class="pagination-item"><a href="{{ $paginator->previousPageUrl() }}&date={{ request()->date }}" rel="prev">&laquo;</a></li>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
    {{-- "Three Dots" Separator --}}
    @if (is_string($element))
    <li class="pagination-item disabled"><span>{{ $element }}</span></li>
    @endif

    {{-- Array Of Links --}}
    @if (is_array($element))
    @foreach ($element as $page => $url)
    @if ($page == $paginator->currentPage())
    <li class="pagination-item active"><span>{{ $page }}</span></li>
    @else
    <li class="pagination-item"><a href="{{ $url }}&date={{ request()->date }}">{{ $page }}</a></li>
    @endif
    @endforeach
    @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
    <li class="pagination-item"><a href="{{ $paginator->nextPageUrl() }}&date={{ request()->date }}" rel="next">&raquo;</a></li>
    @else
    <li class="pagination-item disabled"><span>&raquo;</span></li>
    @endif
</ul>
