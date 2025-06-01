
function ajouterAuPanier(nom, prix, id) {
  let panier = JSON.parse(localStorage.getItem('panier')) || [];
  const index = panier.findIndex(p => p.nom === nom);
  if (index > -1) {
    panier[index].quantite += 1;
  } else {
    panier.push({
      id: id || 0,
      nom: nom,
      prix: prix,
      quantite: 1
    });
  }
  localStorage.setItem('panier', JSON.stringify(panier));
  alert(nom + " ajouté au panier !");
}

function afficherPanier() {
  let panier = JSON.parse(localStorage.getItem('panier')) || [];
  let contenu = '';
  let total = 0;
  panier.forEach((item, i) => {
    const ligne = `<tr>
      <td>${item.nom}</td>
      <td>${item.quantite}</td>
      <td>${item.prix.toFixed(2)} €</td>
      <td>${(item.quantite * item.prix).toFixed(2)} €</td>
      <td><button onclick="retirerDuPanier(${i})">Retirer</button></td>
    </tr>`;
    contenu += ligne;
    total += item.quantite * item.prix;
  });
  document.getElementById('contenu-panier').innerHTML = contenu;
  document.getElementById('total-panier').textContent = total.toFixed(2) + " €";
}

function retirerDuPanier(index) {
  let panier = JSON.parse(localStorage.getItem('panier')) || [];
  panier.splice(index, 1);
  localStorage.setItem('panier', JSON.stringify(panier));
  afficherPanier();
}
