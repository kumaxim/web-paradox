<div>
    <form wire:submit.prevent="submit">
        <div class="mb-3">
            <label for="domain_list">Domain List:</label>
            <textarea name="domain_list" wire:model="domainList" placeholder="Input domain(s) here..." class="form-control" id="domain_list" cols="30" rows="10"></textarea>
            @error('domainList') <div class="mt-3 alert alert-danger">{{ $message }}</div> @enderror
        </div>
        <div class="d-flex justify-content-center">
            <button class="btn btn-success" type="submit">Check</button>
        </div>
    </form>
    <div id="sendRemoteRequests">@isset($jsRunner) {!! $jsRunner !!} @endisset</div>
    <table class="table" id="checkedDomainList" style="display: none">
        <thead>
        <tr>
            <th scope="col">Domain</th>
            <th scope="col">Expired</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script type="text/javascript">
        function queueDomains(domains) {
            let promises = []

            domains.forEach((hostname) => {
                promises.push(
                    window.axios.post('/whois', {
                        domain: hostname,
                    }).then((response) => {
                        return response
                    }).catch((error) => {
                        return error
                    })
                )
            })

            return promises
        }

        async function queryWhoisData(promisesList) {
            const requestWrapper = document.getElementById('sendRemoteRequests')
            while (requestWrapper.firstChild) {
                requestWrapper.removeChild(requestWrapper.lastChild)
            }

            const resultTable = document.getElementById('checkedDomainList');
            resultTable.style.display = 'table'

            const resultTBody = resultTable.querySelector('tbody');
            while(resultTBody.firstChild) {
                resultTBody.removeChild(resultTBody.lastChild)
            }

            await Promise.all(promisesList)
                .then(function (results) {
                    if(typeof results === undefined) {
                        console.log(results)

                        return;
                    }

                    results.forEach((el) => {
                        if (!el.hasOwnProperty('data')) {
                            console.log('=== Invalid response format. Field "data" are mandatory ===')
                            console.log(el)

                            return;
                        }

                        if (!el.data.hasOwnProperty('domain') || !el.data.hasOwnProperty('valid')) {
                            console.log('=== Invalid response format. Fields "domain" and "valid" are mandatory ===')
                            console.log(el.data)

                            return;
                        }

                        resultTable.style.display = 'table'

                        const newTableLine = document.createElement('tr');
                        resultTBody.appendChild(newTableLine)

                        const newTableCellDomain = document.createElement('td');
                        newTableLine.appendChild(newTableCellDomain)
                        const newCellDomainText = document.createTextNode(el.data.domain)
                        newTableCellDomain.appendChild(newCellDomainText)

                        const newTableCellExpiration = document.createElement('td');
                        newTableLine.appendChild(newTableCellExpiration)

                        if (el.data.hasOwnProperty('valid') && el.data.valid) {
                            if (el.data.hasOwnProperty('registered') && el.data.registered) {
                                if (el.data.hasOwnProperty('expires') && el.data.expires) {
                                    const newCellExpirationText = document.createTextNode(el.data.expires)
                                    newTableCellExpiration.appendChild(newCellExpirationText)

                                    return;
                                }

                                const newCellExpirationNotFound = document.createTextNode('Unknown')
                                newTableCellExpiration.appendChild(newCellExpirationNotFound)

                                return;
                            }

                            const newCellFreeDomainText = document.createTextNode('Domain free')
                            newTableCellExpiration.appendChild(newCellFreeDomainText)

                            return;
                        }

                        const newCellDomainInvalid = document.createTextNode('Invalid domain')
                        newTableCellExpiration.appendChild(newCellDomainInvalid)
                    })
                });
        }
    </script>
</div>
