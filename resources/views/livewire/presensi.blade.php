<div>
    <div class="container mx-auto max-w-sm">
        <div class="bg-white p-6 rounded-lg shadow-lg mt-3">
            <div class="grid grid-cols-1 gap-6 mb-6">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Informasi Pegawai</h2>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <p class="mb-2"><strong>Nama pegawai : </strong> {{Auth::user()->name}}</p>
                        <p><strong>Kantor : </strong>{{$schedule->office->name}}</p>
                        <p><strong>Shift : </strong>{{$schedule->shift->name}} ({{$schedule->shift->start_time}}) -
                            ({{$schedule->shift->end_time}})</p>
                        @if ($schedule->is_wfa)
                            <p class="text-red-500"><strong>Status : Work From Anywhere</strong></p>
                        @else
                            <p><strong>Status : Work From Office</strong></p>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-100 p-4 rounded-lg mt-4">
                            <h4 class="text-l font-bold mb-2">Waktu Datang</h4>
                            @if ($attendance)
                                <p>{{$attendance->start_time}}</p>
                            @else
                                <p>-</p>
                            @endif
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg mt-4">
                            <h4 class="text-l font-bold mb-2">Waktu Pulang</h4>
                            @if ($attendance)
                                <p>{{$attendance->end_time}}</p>
                            @else
                                <p>-</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-bold mb-2">Presensi</h2>
                    <div id="map" class="mb-4 rounded-lg border border-gray-300" wire:ignore></div>
                    @if (session()->has('error'))
                        <div style="color: red; padding: 10px; border: 1px solid red; background-color: #fdd">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form class="row g-3 mt-3" wire:submit="store" enctype="multipart/form-data">
                        <button type="button" onclick="tagLocation()"
                            class="px-4 py-2 bg-blue-500 text-white rounded">Tag
                            Location</button>
                        @if ($isInsideRadius)
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded">Submit Presensi</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        let map;
        let lang;
        let lng;
        const center = [{{$schedule->office->latitude}}, {{$schedule->office->longitude}}]
        const radius = {{$schedule->office->radius}}
            let marker;
        let component;
        document.addEventListener('livewire:initialized', function () {
            component = @this;
            map = L.map('map').setView([{{$schedule->office->latitude}}, {{$schedule->office->longitude}}], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);


            const circle = L.circle(center, {
                color: 'blue',
                fillColor: '#f03',
                fillOpacity: 0.5,
                radius: radius
            }).addTo(map);
        });
        function tagLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    lat = position.coords.latitude;
                    lng = position.coords.longitude;
                    if (marker === null) {
                        map.removeLayer(marker);
                    }

                    marker = L.marker([lat, lng]).addTo(map);
                    map.setView([lat, lng], 13);

                    if (isWithinRadius(lat, lng, center, radius)) {
                        component.set('isInsideRadius', true);
                        component.set('latitude', lat);
                        component.set('longitude', lng);
                        // alert('Anda berada di kantor');
                    }
                })
            } else {
                alert('Geolocation is not supported by this browser.')
            }
        }

        function isWithinRadius(lat, lng, center, radius) {
            // let distance = map.distance([lat, lng], center);
            // return distance <= radius;
            const is_wfa = {{$schedule->is_wfa}};
            if (is_wfa) {
                return true;
            } else {
                let distance = map.distance([lat, lng], center);
                return distance <= radius;
            }
        }
    </script>
</div>