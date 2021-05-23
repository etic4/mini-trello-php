<article id="show-card" class="display-none">
    <header class="mb-5">
        <div class="is-align-items-baseline">
            <h3 class="title">Card '<span id="card-title"></span>'</h3>

            <div class="has-text-grey mb-1">
                Created <span id="card-created-intvl"></span> by <strong class="has-text-info">'<span id="card-author"></span>'</strong>. <span id="card-modified-intvl"></span>
            </div>
            <div class="has-text-grey ">
                This card is on the board
                <a id="card-board-url" href="">"<strong class="has-text-info">'<span id="card-board-title"></span></strong>"</a>,
                column "<strong class="has-text-info">'<span id="card-column-title"></span></strong>" at position '<span id="card-position"></span>
            </div>
        </div>
    </header>
    <section>
        <p class="title is-5 mb-2">Body</p>
        <textarea id="card-body" class="textarea has-fixed-size has-text-black mb-4" rows="3" disabled></textarea>

        <p class="title is-5 mb-2">Due date</p>
        <p id="card-due-date" class="mb-4"></p>

        <p class="title is-5 mb-2">Participants</p>
        <ul id="card-participants" class="ml-2 mb-4"></ul>

        <p class="title is-5 mb-2">Comments</p>
        <ul id="card-comments" class="ml-2 mb-4"></ul>
    </section>
</article>
