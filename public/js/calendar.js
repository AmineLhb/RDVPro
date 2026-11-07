function calendarManager(el) {
    const customers = JSON.parse(el.getAttribute('data-customers'));
    const services = JSON.parse(el.getAttribute('data-services'));
    const resources = JSON.parse(el.getAttribute('data-resources'));
    const oldForm = JSON.parse(el.getAttribute('data-old-form'));
    const oldEditForm = JSON.parse(el.getAttribute('data-old-edit-form'));
    const hasErrors = el.getAttribute('data-errors') === 'true';
    const oldId = el.getAttribute('data-old-id');
    const oldStartsAt = el.getAttribute('data-old-starts-at');
    const oldEndsAt = el.getAttribute('data-old-ends-at');
    const eventsUrl = el.getAttribute('data-events-url');

    return {
        calendar: null,
        showCreateModal: false,
        showEditModal: false,
        errorMessage: '',
        
        createStartsPicker: null,
        createEndsPicker: null,
        editStartsPicker: null,
        editEndsPicker: null,
        
        customers: customers,
        services: services,
        resources: resources,

        selectedAppointmentId: null,

        formData: oldForm,
        editFormData: oldEditForm,

        init() {
            const calendarEl = document.getElementById('calendar');
            if (!calendarEl) return;
            
            this.calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'fr',
                firstDay: 1, // Start week on Monday
                slotMinTime: '08:00:00', // Open at 8 AM
                slotMaxTime: '21:00:00', // Close at 9 PM
                allDaySlot: false,
                slotDuration: '00:15:00',
                snapDuration: '00:05:00',
                nowIndicator: true,
                editable: true, // Allow drag & drop / resize
                eventResizableFromStart: true, // Allow resizing from both top and bottom
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: "Aujourd'hui",
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour'
                },
                events: eventsUrl + '?format=json',
                
                // Custom event HTML renderer
                eventContent: function(arg) {
                    const status = arg.event.extendedProps.status;
                    const resourceColor = arg.event.backgroundColor || '#3d5a3d';
                    
                    // Status dot colors mapping
                    let dotColor = '#8a8273'; // muted
                    if (status === 'confirmed') dotColor = '#3d5a3d'; // olive
                    if (status === 'completed') dotColor = '#52734f'; // active olive
                    if (status === 'pending') dotColor = '#d4a017'; // gold
                    if (status === 'cancelled') dotColor = '#c2683f'; // clay
                    if (status === 'no_show') dotColor = '#1a1410'; // ink
                    
                    const container = document.createElement('div');
                    container.className = 'w-full h-full flex flex-col justify-between';
                    
                    // Colored left accent bar representing the staff member
                    container.style.borderLeft = `3.5px solid ${resourceColor}`;
                    container.style.paddingLeft = '6px';
                    container.style.marginLeft = '-4px';
                    
                    const topRow = document.createElement('div');
                    topRow.className = 'flex items-center justify-between w-full min-w-0';
                    
                    const titleEl = document.createElement('div');
                    titleEl.className = 'font-bold truncate text-[10.5px] text-ink';
                    titleEl.innerText = arg.event.extendedProps.customer_name || arg.event.title;
                    
                    const dot = document.createElement('span');
                    dot.className = 'w-2 h-2 rounded-full flex-shrink-0 ml-1.5';
                    dot.style.backgroundColor = dotColor;
                    dot.style.boxShadow = `0 0 4px ${dotColor}`;
                    
                    topRow.appendChild(titleEl);
                    topRow.appendChild(dot);
                    
                    const bottomRow = document.createElement('div');
                    bottomRow.className = 'text-[9.5px] text-[#8a8273] font-medium truncate mt-0.5';
                    bottomRow.innerText = arg.event.extendedProps.service_name || '';
                    
                    container.appendChild(topRow);
                    container.appendChild(bottomRow);
                    
                    return { domNodes: [container] };
                },
                
                // Slot click -> Trigger modal creation
                dateClick: (info) => {
                    this.openCreateModal(info.date);
                },

                // Event click -> Trigger modal view/edit
                eventClick: (info) => {
                    this.openEditModal(info.event);
                },

                // Drag and Drop reschedule
                eventDrop: (info) => {
                    this.handleReschedule(info);
                },

                // Duration Resizing reschedule
                eventResize: (info) => {
                    this.handleReschedule(info);
                }
            });

            this.calendar.render();

            // Initialize Flatpickr instances
            this.createStartsPicker = flatpickr(this.$refs.createStarts, {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                locale: "fr",
                onChange: (selectedDates, dateStr) => {
                    this.formData.starts_at = dateStr;
                    this.onTimeChange('create');
                }
            });

            this.createEndsPicker = flatpickr(this.$refs.createEnds, {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                locale: "fr",
                onChange: (selectedDates, dateStr) => {
                    this.formData.ends_at = dateStr;
                }
            });

            this.editStartsPicker = flatpickr(this.$refs.editStarts, {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                locale: "fr",
                onChange: (selectedDates, dateStr) => {
                    this.editFormData.starts_at = dateStr;
                    this.onTimeChange('edit');
                }
            });

            this.editEndsPicker = flatpickr(this.$refs.editEnds, {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                locale: "fr",
                onChange: (selectedDates, dateStr) => {
                    this.editFormData.ends_at = dateStr;
                }
            });

            const params = new URLSearchParams(window.location.search);
            if (params.get('create') === '1') {
                this.openCreateModal();
            }
            const editId = params.get('edit');
            if (editId) {
                fetch(eventsUrl + '?format=json')
                    .then(res => res.json())
                    .then(events => {
                        const ev = events.find(e => e.id == editId);
                        if (ev) {
                            this.openEditModal({
                                id: ev.id,
                                start: new Date(ev.start),
                                end: new Date(ev.end),
                                extendedProps: ev.extendedProps
                            });
                        }
                    });
            }

            if (hasErrors) {
                if (oldId) {
                    this.selectedAppointmentId = oldId;
                    this.showEditModal = true;
                    this.$nextTick(() => {
                        if (this.editStartsPicker) this.editStartsPicker.setDate(oldStartsAt);
                        if (this.editEndsPicker) this.editEndsPicker.setDate(oldEndsAt);
                    });
                } else {
                    this.showCreateModal = true;
                    this.$nextTick(() => {
                        if (this.createStartsPicker) this.createStartsPicker.setDate(oldStartsAt);
                        if (this.createEndsPicker) this.createEndsPicker.setDate(oldEndsAt);
                    });
                }
            }
        },

        // Format JavaScript Date to string 'YYYY-MM-DD HH:MM' for Flatpickr inputs
        formatDateTimeLocal(date) {
            if (!date) return '';
            const d = typeof date === 'string' ? new Date(date.replace(' ', 'T')) : new Date(date);
            if (isNaN(d.getTime())) return '';
            const pad = (num) => String(num).padStart(2, '0');
            const yyyy = d.getFullYear();
            const mm = pad(d.getMonth() + 1);
            const dd = pad(d.getDate());
            const hh = d.getHours();
            const min = d.getMinutes();
            return `${yyyy}-${mm}-${dd} ${pad(hh)}:${pad(min)}`;
        },

        // Format JS Date to database string format 'YYYY-MM-DD HH:MM:SS'
        formatDateTimeDb(date) {
            if (!date || isNaN(new Date(date).getTime())) return '';
            const d = new Date(date);
            const pad = (num) => String(num).padStart(2, '0');
            const yyyy = d.getFullYear();
            const mm = pad(d.getMonth() + 1);
            const dd = pad(d.getDate());
            const hh = d.getHours();
            const min = d.getMinutes();
            const ss = d.getSeconds();
            return `${yyyy}-${mm}-${dd} ${pad(hh)}:${pad(min)}:${pad(ss)}`;
        },

        openCreateModal(date = null) {
            this.errorMessage = '';
            
            // Clear form data
            this.formData = {
                customer_id: '',
                service_id: '',
                resource_id: this.resources.length > 0 ? this.resources[0].id : '',
                starts_at: '',
                ends_at: '',
                price: '',
                status: 'confirmed',
                note: ''
            };

            if (date) {
                this.formData.starts_at = this.formatDateTimeLocal(date);
                // Default end time to start + 30 minutes
                const endDate = new Date(date.getTime() + 30 * 60 * 1000);
                this.formData.ends_at = this.formatDateTimeLocal(endDate);
            } else {
                const now = new Date();
                now.setMinutes(0, 0, 0); // round to current hour
                this.formData.starts_at = this.formatDateTimeLocal(now);
                const endDate = new Date(now.getTime() + 30 * 60 * 1000);
                this.formData.ends_at = this.formatDateTimeLocal(endDate);
            }

            if (this.createStartsPicker) {
                this.createStartsPicker.setDate(this.formData.starts_at);
            }
            if (this.createEndsPicker) {
                this.createEndsPicker.setDate(this.formData.ends_at);
            }

            this.showCreateModal = true;
        },

        openEditModal(event) {
            this.errorMessage = '';
            this.selectedAppointmentId = event.id;

            const props = event.extendedProps;
            
            this.editFormData = {
                customer_id: props.customer_id,
                service_id: props.service_id,
                resource_id: props.resource_id,
                starts_at: this.formatDateTimeLocal(event.start),
                ends_at: this.formatDateTimeLocal(event.end),
                price: props.price,
                status: props.status,
                note: props.note || ''
            };

            if (this.editStartsPicker) {
                this.editStartsPicker.setDate(this.editFormData.starts_at);
            }
            if (this.editEndsPicker) {
                this.editEndsPicker.setDate(this.editFormData.ends_at);
            }

            this.showEditModal = true;
        },

        onServiceChange(type) {
            const serviceId = type === 'create' ? this.formData.service_id : this.editFormData.service_id;
            const service = this.services.find(s => s.id == serviceId);
            
            if (service) {
                if (type === 'create') {
                    this.formData.price = service.price;
                    if (this.formData.starts_at) {
                        const startDate = new Date(this.formData.starts_at.replace(' ', 'T'));
                        if (!isNaN(startDate.getTime())) {
                            const endDate = new Date(startDate.getTime() + service.duration_minutes * 60 * 1000);
                            this.formData.ends_at = this.formatDateTimeLocal(endDate);
                            if (this.createEndsPicker) {
                                this.createEndsPicker.setDate(this.formData.ends_at);
                            }
                        }
                    }
                } else {
                    this.editFormData.price = service.price;
                    if (this.editFormData.starts_at) {
                        const startDate = new Date(this.editFormData.starts_at.replace(' ', 'T'));
                        if (!isNaN(startDate.getTime())) {
                            const endDate = new Date(startDate.getTime() + service.duration_minutes * 60 * 1000);
                            this.editFormData.ends_at = this.formatDateTimeLocal(endDate);
                            if (this.editEndsPicker) {
                                this.editEndsPicker.setDate(this.editFormData.ends_at);
                            }
                        }
                    }
                }
            }
        },

        onTimeChange(type) {
            const serviceId = type === 'create' ? this.formData.service_id : this.editFormData.service_id;
            const service = this.services.find(s => s.id == serviceId);
            const duration = service ? service.duration_minutes : 30; // fallback to 30 mins

            if (type === 'create') {
                if (this.formData.starts_at) {
                    const startDate = new Date(this.formData.starts_at.replace(' ', 'T'));
                    if (!isNaN(startDate.getTime())) {
                        const endDate = new Date(startDate.getTime() + duration * 60 * 1000);
                        this.formData.ends_at = this.formatDateTimeLocal(endDate);
                        if (this.createEndsPicker) {
                            this.createEndsPicker.setDate(this.formData.ends_at);
                        }
                    }
                }
            } else {
                if (this.editFormData.starts_at) {
                    const startDate = new Date(this.editFormData.starts_at.replace(' ', 'T'));
                    if (!isNaN(startDate.getTime())) {
                        const endDate = new Date(startDate.getTime() + duration * 60 * 1000);
                        this.editFormData.ends_at = this.formatDateTimeLocal(endDate);
                        if (this.editEndsPicker) {
                            this.editEndsPicker.setDate(this.editFormData.ends_at);
                        }
                    }
                }
            }
        },

        handleReschedule(info) {
            this.errorMessage = '';
            const event = info.event;
            
            const payload = {
                starts_at: this.formatDateTimeDb(event.start),
                ends_at: this.formatDateTimeDb(event.end),
                resource_id: event.extendedProps.resource_id
            };

            fetch(eventsUrl + '/' + event.id + '/reschedule', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                // Update successfully
                console.log(data.message);
            })
            .catch(err => {
                // Show error and revert move
                let msg = "Erreur lors du déplacement du rendez-vous.";
                if (err.errors && err.errors.starts_at) {
                    msg = err.errors.starts_at[0];
                } else if (err.message) {
                    msg = err.message;
                }
                this.errorMessage = msg;
                info.revert();
            });
        },

        confirmDelete() {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Cette action supprimera définitivement ce rendez-vous !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3d5a3d',
                cancelButtonColor: '#8a8273',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm').submit();
                }
            });
        }
    };
}
