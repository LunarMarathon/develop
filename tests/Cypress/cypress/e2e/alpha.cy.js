describe('test default site files', () => {
  it('the index page uses welcome page template', () => {
    cy.visit('index.html')
    cy.contains('You\'re running on HydePHP')
  })

  it('has a 404 page', () => {
    cy.visit('404.html')
    cy.contains('404')
    cy.contains('Sorry, the page you are looking for could not be found.')
    cy.contains('Go Home')
  })
})