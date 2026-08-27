fetch('../cimes_api/index_api_head.php?query=navbar')
  .then(response => response.json())
  .then(data => {
    console.log(data);
    let planDetail = ``;
    data.forEach(ligne => {
      // Vérifier si ligne.sub_items existe et est un tableau
      if (Array.isArray(ligne.sub_items)) {
        planDetail += `<ul>
                        <li>
                        <ul class="nested active" style="display: flex;">
                            <li>
                            <span class="caret caret-down">${ligne.name}</span>
                            <ul class="nested active">`;

        ligne.sub_items.forEach(subItem => {
          planDetail += `<li><a href="${subItem.url}">${subItem.name}</a></li>`;
        });

        planDetail += `     </ul>
                            </li>
                        </ul>
                        </li>
                    </ul>`;
      }
    });})