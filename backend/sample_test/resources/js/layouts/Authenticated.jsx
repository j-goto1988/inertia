export default function Authenticated({ children }) {
  return (
    <div>
      <header>Header</header>
      <main>{children}</main>
    </div>
  )
}