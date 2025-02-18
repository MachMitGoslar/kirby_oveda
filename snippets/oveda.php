<?php /************** OVEDA - View Snippet*******/
    /* $oveda_search Oveda Class */
    /* $date_search:Bool - Show date search */
    /* $recurrent:Bool - Show Reccurent Toogle */

    if ($oveda_search->exists() && $search = $oveda_search->toObject()) {


    if ($oveda_search->organziers() !== "") {
        $organizer_id = $search->organizer();
    }

    if ($search->start() != "") {


        $start = $search->start()->format("Y-m-d")->toString();
    } else {
        $start = (string) (new DateTime("now"))->format("Y-m-d");

    }

    if ($position = $search->location()->yaml()) {
        if (!empty($position['lat']) && !empty($position['lon']) && $search->distance()->exists()) {
            $latitude = $position['lat'];
            $longitude = $position['lon'];
            $distance = $search->distance();
        }
    }

    $end = $search->end()->format("Y-m-d")->toString();

}
/* let results   = parseInt(<?=#$oveda_search->results?>);*/
?>


<div class="mt-4 mb-4 text-gray-900">

    <h2 class="text-title mb-2"> Veranstaltungen </h2>
    
    
    <?php if ($date_search || $recurrent): ?>

        <div class="grid grid-cols-4">


            <?php if ($date_search): ?>
                <div class="from-control col-span-2 mb-5">
                    <label for="date_from block mb-2 text-sm font-medium text-gray-900 dark:text-white">Suche ab:</label>
                    <input type="date" id="date_from"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        oninput="dateFromSet(event)">

                </div>

                <div class="col-span-4 md:col-span-2 mb-5">

                </div>
            <?php endif; ?>

            <?php if ($recurrent): ?>
                <div class="col-span-4 md:col-span-2 mb-5">

                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="rec_check" value="" class="sr-only peer" onchange="toggleReccurend(event)">
                        <div
                            class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-gold-600">
                        </div>
                        <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Zeige wiederkehrende
                            Termine</span>
                    </label>
                </div>
            <?php endif; ?>


        </div>
    <?php endif; ?>

    <div class="content_grid events mb-4">


    </div>

    <p class="text-black text-caption float-end "> powered by: <a class="text-gold" href="https://oveda.de">Oveda</a> Goslar </p>
    <div class="loading_button">
        <button
            class="load-more" data-type="secondary" data-style="pill" >Mehr anzeigen</button>
    </div>



    

</div>

<script>
    //
    const element = document.querySelector('.events');
    const button = document.querySelector('.load-more');
    const date_from_element = document.querySelector('#date_from');
    const check_rec = document.querySelector("#rec_check");


    let page = 1;
    let results = 6;
    let exclude_reccurend = false;

    <?php if($recurrent): ?>
        check_rec.checked = !exclude_reccurend;
    <?php endif ; ?>

    let organizer_id = <?= $organizer_id ? 'parseInt(' . $organizer_id . ')' : null ?>;
    let start = "<?= $start ? $start : null ?>";
    <?php if($date_search): ?>
        date_from_element.value = start;
    <?php endif; ?>



    let end = "<?= $end ? $end : null ?>";
    let latitude = "<?= isset($latitude) ? $latitude : null ?>";
    let longitude = "<?= isset($longitude) ? $longitude : null ?>";
    let distance = "<?= isset($distance) ? $distance->toInt() : null ?>";

    const fetchEvents = async () => {
        let url = "<?= url('oveda.json') ?>?"
        url += "page=" + page;
        url += "&results=" + results;
        url += start ? "&date_from=" + start : "";
        url += end ? "&date_to=" + end : "";
        url += organizer_id ? "&organizer_id=" + organizer_id : "";
        url += latitude ? "&latitude=" + latitude : "";
        url += longitude ? "&longitude=" + longitude : "";
        url += distance ? "&distance=" + distance : "";
        url += "&exclude_recurring=" + exclude_reccurend;
        //let url = `${window.location.href.split('#')[0]}.json/page:${page}`; // see info
        try {
            const response = await fetch(url);
            return { html, more } = await response.json();
        } catch (error) {
            console.log('Fetch error: ', error);
        }
    }

    const fetchMoreEvents = async () => {
        var { html, more } = await fetchEvents();
        button.disabled = !more;
        element.innerHTML += html;
        page++;
    }

    const fetchEventsAnew = async () => {
        loading_animation();
        var { html, more } = await fetchEvents();
        button.hidden = !more;
        element.innerHTML = html;
        page = 2;
    }


    function toggleReccurend(event) {
        exclude_reccurend = !event.srcElement.checked
        fetchEventsAnew();
    }

    function dateFromSet(event) {
        let date_from = new Date(event.srcElement.value);
        start = date_from.toISOString().split("T")[0];
        page = 1;
        fetchEventsAnew();
    }

    function loading_animation() {
        element.innerHTML = `
            <div class="flex items-center justify-center h-60 col-span-3 w-screen">
            <button class="relative h-10 inline-flex items-center justify-center p-3 mb-2 me-2 overflow-hidden text-2xl font-thin text-white rounded-lg group bg-gradient-to-br from-gold-600 to-gold-500 group-hover:from-gold-600 group-hover:to-gold-500 hover:text-white dark:text-white" disabled>
            <svg class=" animate-spin h-8 w-8 mr-3 fill-white" viewBox="0 0 24 24">
                <path xmlns="http://www.w3.org/2000/svg" d="M12 22C17.5228 22 22 17.5228 22 12H19C19 15.866 15.866 19 12 19V22Z"/>
                <path xmlns="http://www.w3.org/2000/svg" d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z" />
            </svg>
            Processing
            </button>
            </div>

        `
    }


    button.addEventListener('click', fetchMoreEvents);

    console.log("Elements: ", element.childElementCount)
    if (element.childElementCount == 0) {
        fetchEventsAnew();
    }
</script>